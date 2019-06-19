<?php


namespace App\Service;


use App\DTO\ClientDTO;
use App\Repository\ClientRepositoryInterface;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use App\Repository\TownRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Exception\ValidationExeption;
use Core\Session\Session;

class ClientService implements ClientServiceInterface
{
    public function addClient(ClientRepositoryInterface $clientRepo, StreetRepositoryInterface $streetRepo,
                              NeighborhoodRepositoryInterface $neighborhoodRepo, TownRepositoryInterface $townRepo,
                              array $post): array
    {
        try {
            $session = new Session();

            $realCsrfToken = $session->get('csrf_token');

            if ( $post['csrf_token'] === $realCsrfToken ) {
                $dataBinder = new DataBinder();
                $clientData = new ClientDTO();
                /** @var ClientDTO $client */
                $client = $dataBinder->bindData($post, $clientData);

                $streetId = $streetRepo->getStreetByName($client->getStreet());

                if ( $streetId === null ) {
                    return ['status' => 'error', 'description' => 'Улицата не съществува'];
                }

                if ( $townRepo->checkForStreetInTown($client->getTown(), $streetId->getId()) === false ) {
                    return ['status' => 'error', 'description' => 'Улицата и града не съвпадат'];
                }

                $client->setStreet($streetId->getId());

                $isHaveNeighborhood = $neighborhoodRepo->checkTownHaveNeighborhood($client->getTown());

                if ( $isHaveNeighborhood === true ) {
                    if ($isHaveNeighborhood === true && $client->getNeighborhood() === null) {
                        return ['status' => 'error', 'description' => 'Трябва да зададете квартал'];
                    }

                    $neighborhoodId = $neighborhoodRepo->getNeighborhoodId($client->getNeighborhood());

                    if ($neighborhoodId === null) {
                        return ['status' => 'error', 'description' => 'Квартала не е валиден'];
                    }

                    if ($neighborhoodRepo->checkForValidNeighborhood($client->getTown(), $neighborhoodId) === false) {
                        return ['status' => 'error', 'description' => 'Квартала не е валиден'];
                    }

                    $client->setNeighborhood($neighborhoodId);
                }else {
                    $client->setNeighborhood(null);
                }

                if ( $client->getDescription() === '' ) {
                    $client->setDescription(null);
                }

                $isAdd = $clientRepo->addClient($client);

                if ( $isAdd ) {
                    $session->addFlashMessage('success', 'Успешно добавихте нов клиент');
                    return ['status' => 'success'];
                }
            } else {
                return ['status' => 'error', 'description' => 'Грешен токен!!!'];
            }
        }catch (ValidationExeption $e) {
            return ['status' => 'error', 'description' => $e->getMessage()];
        }
    }
}