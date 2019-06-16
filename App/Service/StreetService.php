<?php


namespace App\Service;


use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
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

}