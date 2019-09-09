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

    public function getIncomeAccount(AbonamentRepositoryInterface $abonamentRepository, $abonamentId, $csrfToken): array
    {
        try {
            Validator::validateInt($abonamentId);

            $session = new Session();

            if ($session->get('csrf_token') === $csrfToken) {
                $abonamentPrice = (int)$abonamentRepository->getAbonamentPrice($abonamentId)->getPrice();
                $daysInMounth = (int)date('t');
                $currentDate = (int)date('d') - 1;

                $pricePerDay = $abonamentPrice / $daysInMounth;
                $priceToEndOfMounth = ($daysInMounth - $currentDate) * $pricePerDay;

                return [
                    'status'             => 'success',
                    'priceToEndOfMounth' => ceil($priceToEndOfMounth),
                    'start'              => time(),
                    'end'                => strtotime(date('Y-n-t 23:59:59')),
                    'creditLimit'        => $abonamentPrice * 2
                ];
            } else {
                return [
                    'status' => 'error',
                    'description' => 'Грешен токен'
                ];
            }
        }catch (ValidationExeption $exception) {
            return [
                'status'      => 'error',
                'description' => $exception->getMessage()
            ];
        }
    }

}