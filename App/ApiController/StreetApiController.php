<?php


namespace App\ApiController;


use App\Repository\NeighborhoodRepository;
use App\Repository\StreetRepository;
use App\Service\StreetService;
use Core\Controller\AbstractController;

class StreetApiController extends AbstractController
{

    public function getTownStreets($db, $id, $csrfToken)
    {
        $this->validateAccess(true);

        $neighborhoodRepo = new NeighborhoodRepository($db);
        $streetRepo       = new StreetRepository($db);
        $streetService    = new StreetService($streetRepo);

        $responce      = $streetService->getStreetsInTown($streetRepo, $neighborhoodRepo, $id, $csrfToken);

        return $this->jsonResponce($responce);
    }
}