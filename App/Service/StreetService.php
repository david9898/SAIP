<?php


namespace App\Service;


use App\DTO\StreetDTO;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Session\Session;

class StreetService implements StreetServiceInterface
{

    public function getStreetsInTown(StreetRepositoryInterface $streetRepo, NeighborhoodRepositoryInterface $neighborhoodRepo, $townId, $csrfToken): array
    {
        $session       = new Session();
        $realCsrfToken = $session->get('csrf_token');

        if ( $realCsrfToken === $csrfToken ) {
            $streets       = $streetRepo->getTownStreets($townId);
            $neighborhoods = $neighborhoodRepo->getTownNeighborhoods($townId);

            foreach ( $streets as $item ) {
                $arr['streets'][] = $item;
            }

            foreach ( $neighborhoods as $item ) {
                $arr['neighborhoods'][] = $item;
            }

            return [
                'status'      => 'success',
                'responce'    => $arr
            ];

        }else {
            return [
                'status' => 'error',
                'description' => 'Wrong csrfToken!!!'
            ];
        }
    }

    public function addStreetInTown(StreetRepositoryInterface $streetRepo, $postArr): bool
    {
        $session = new Session();

        if ( $postArr['csrf_token'] === $session->get('csrf_token') ) {
            $street    = new StreetDTO();
            /** @var StreetDTO $newStreet */
            $newStreet = DataBinder::bindData($postArr, $street);

            $explodeStreet = explode(' ', $newStreet->getName());

            if ( $explodeStreet[0] !== 'ул.' ) {
                $newStreet->setName('ул. ' . $newStreet->getName());
            }

            $streetRepo->addStreet($newStreet->getName());
            $streetRepo->addRelationTownStreet(
                $streetRepo->getStreetByName($newStreet->getName())->getId(),
                $newStreet->getTownId()
            );

            $session->addFlashMessage('success', 'Успешно добавихте нова улица!');

            return true;
        }else {
            $session->addFlashMessage('error', 'Грешен токен!');
            return false;
        }
    }

}