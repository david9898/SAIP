<?php


namespace App\Service;


use App\DTO\ClientDTO;
use App\DTO\PaymentDTO;
use App\Repository\AbonamentRepositoryInterface;
use App\Repository\ClientRepository;
use App\Repository\ClientRepositoryInterface;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use App\Repository\TownRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Exception\ValidationExeption;
use Core\Request\Request;
use Core\Session\Session;
use Core\Validation\Validator;

class ClientService implements ClientServiceInterface
{
    public function addClient(ClientRepositoryInterface $clientRepo, StreetRepositoryInterface $streetRepo,
                              NeighborhoodRepositoryInterface $neighborhoodRepo, TownRepositoryInterface $townRepo,
                              AbonamentRepositoryInterface $abonamentRepo, PaymentRepositoryInterface$paymentRepo,
                              InvoiceRepositoryInterface $invoiceRepo, AbonamentServiceInterface $abonamentService,
                              $post): array
    {
        try {
            $session = new Session();
            $post    = json_decode($post, true);

            $realCsrfToken = $session->get('csrf_token');

            if ( $post['csrf_token'] === $realCsrfToken ) {
                $clientData = new ClientDTO();
                /** @var ClientDTO $client */
                $client = DataBinder::bindData($post, $clientData);

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

                Validator::validateInt($client->getStreetNumber());
                Validator::validateInt($client->getAbonament());
                Validator::validateInt($client->getDateRegister());
                Validator::validateInt($client->getTown());
                Validator::validateInt($client->getStreet());
                Validator::validateInt($client->getCreditLimit());
                Validator::validateBgCharacters($client->getFirstName());
                Validator::validateBgCharacters($client->getLastName());
                Validator::validateEmail($client->getEmail());
                Validator::validatePhone($client->getPhone());

                if ( $client->getDescription() !== null ) {
                    Validator::validateBgCharacters($client->getDescription());
                }

                if ( $client->getNeighborhood() !== null ) {
                    Validator::validateInt($client->getNeighborhood());
                }

                $id = $clientRepo->addClient($client);

                if ( $id ) {
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



    public function getClients(ClientRepositoryInterface $repository, $firstResult, $csrfToken): array
    {
        $session   = new Session();

        if ( $csrfToken !== $session->get('csrf_token')) {
            return [
              'status'      => 'error',
              'description' => 'Грешен токен!'
            ];
        }

        $clients = $repository->getClients($firstResult);

        $res = $this->calculateClientsTimeToBills($clients);

        return [
            'status'  => 'success',
            'clients' => $res
        ];
    }

    public function getSearchFriends(ClientRepositoryInterface $clientRepository, $firstResult, $csrfToken, $pattern): array
    {
        $session = new Session();

        if ( $session->get('csrf_token') === $csrfToken ) {

            if ($pattern !== null) {
                $patterns = explode(' ', urldecode($pattern));

                $clients = $clientRepository->searchFriends($patterns, $firstResult);

                if ($clients !== null) {
                    $responce            = [];
                    $responce['status']  = 'success';
                    $responce['clients'] = $this->calculateClientsTimeToBills($clients);

                    return $responce;
                }else {
                    return [
                        'status' => 'success'
                    ];
                }
            } else {
                $responce            = [];
                $responce['status']  = 'success';
                $clients = $clientRepository->getClients($firstResult);
                $responce['clients'] = $this->calculateClientsTimeToBills($clients);

                return $responce;
            }
        }else {
            return [
              'status'      => 'error',
              'description' => 'Грешен токен!'
            ];
        }
    }

    public function calculateClientsTimeToBills(?\Generator $clients): ?array
    {
        if ( $clients === null ) {
            return null;
        }

        $time      = time();
        $res       = [];

        foreach ($clients as $client) {
            /** @var ClientDTO $client */

            if ( $client['paid'] === null ) {
                $res[] = $client;
                continue;
            }

            $diff           = $client['paid'] - $time;
            $daysDiff       = floor($diff / 86400);

            if ( $diff >= 0 ) {
                $client['invoiceStatus'] = 'paid';
            }else {
                $client['invoiceStatus'] = 'overdue';
            }

            $client['paid'] = $daysDiff;
            $res[]          = $client;

            continue;
        }

        return $res;
    }

}