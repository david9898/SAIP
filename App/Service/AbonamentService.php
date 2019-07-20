<?php


namespace App\Service;


use App\DTO\AbonamentDTO;
use App\Repository\AbonamentRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Session\Session;

class AbonamentService implements AbonamentServiceInterface
{

    public function addAbonament(AbonamentRepositoryInterface $abonamentRepository, $postArr): bool
    {
        $session = new Session();

        if ( $session->get('csrf_token') === $postArr['csrf_token'] ) {
            $abonament    = new AbonamentDTO();

            $newAbonament = DataBinder::bindData($postArr, $abonament);

            $isAbonament = $abonamentRepository->checkIfAbonamentExist($newAbonament->getName());

            if ($isAbonament === null) {
                $abonamentRepository->addAbonament($newAbonament);

                $session->addFlashMessage('success', 'Успешно добавен нов абонамент!');

                return true;
            } else {
                $session->addFlashMessage('error', 'Абонамент с такова име вече съществува!');

                return false;
            }
        }else {
            $session->addFlashMessage('error', 'Грешен токен!');

            return false;
        }
    }

}