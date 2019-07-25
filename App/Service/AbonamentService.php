<?php


namespace App\Service;


use App\DTO\AbonamentDTO;
use App\Repository\AbonamentRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Exception\ValidationExeption;
use Core\Session\Session;
use Core\Validation\Validator;

class AbonamentService implements AbonamentServiceInterface
{

    public function addAbonament(AbonamentRepositoryInterface $abonamentRepository, $postArr): bool
    {
        try {
            $session = new Session();

            if ($session->get('csrf_token') === $postArr['csrf_token']) {
                $abonament = new AbonamentDTO();

                /** @var AbonamentDTO $newAbonament */
                $newAbonament = DataBinder::bindData($postArr, $abonament);

                Validator::validateBgCharacters($newAbonament->getName());
                Validator::validateInt($newAbonament->getPrice());

                if ( $newAbonament->getDescription() !== '' ) {
                    Validator::validateBgCharacters($newAbonament->getDescription());
                }

                $isAbonament = $abonamentRepository->checkIfAbonamentExist($newAbonament->getName());

                if ($isAbonament === null) {
                    $abonamentRepository->addAbonament($newAbonament);

                    $session->addFlashMessage('success', 'Успешно добавен нов абонамент!');

                    return true;
                } else {
                    $session->addFlashMessage('error', 'Абонамент с такова име вече съществува!');

                    return false;
                }
            } else {
                $session->addFlashMessage('error', 'Грешен токен!');

                return false;
            }
        }catch (ValidationExeption $exception) {
            $session->addFlashMessage('error', $exception->getMessage());

            return false;
        }
    }

}