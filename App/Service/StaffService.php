<?php


namespace App\Service;

use App\DTO\StaffDTO;
use App\Repository\StaffRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Exception\ValidationExeption;
use Core\Session\Session;

class StaffService implements StaffServiceInterface
{

    public function login(StaffRepositoryInterface $staffRepo, array $post): bool
    {
        $session      = new Session();

        /** @var StaffDTO $customer */
        $staff   = $staffRepo->getCustomer($post['username']);

        if ( $customer === null ) {
            $session->addFlashMessage('error', 'Грешно потребителско име');
            return false;
        }

        $pass = hash('sha512', $post['password']);

        if ( $pass === $customer->getPassword() ) {
            $session->addFlashMessage('success', 'Добре дошли');
            $userData = [
                'id'       => $staff->getId(),
                'username' => $staff->getUsername(),
                'role'     => $staff->getRole()
            ];

            $session->set('userData', $userData);

            return true;
        }

        $session->addFlashMessage('error', 'Грешна парола');

        return false;
    }

    public function registerStaff(StaffRepositoryInterface $staffRepo, array $post): bool
    {
        $session    = new Session();
        $dataBinder = new DataBinder();

        if ( $session->get('csrf_token') === $post['csrf_token'] ) {
            if ($post['password'] === $post['repeat_password']) {
                $customerData = new StaffDTO();
                /** @var StaffDTO $customer */
                $customer     = $dataBinder->bindData($post, $customerData);
                $newPass      = hash('sha512', $customer->getPassword());
                $customer->setPassword($newPass);

                $staffRepo->addCustomer($customer);
                $session->addFlashMessage('success', 'Успешно добавихте нов служител');

                return true;
        } else {
                $session->addFlashMessage('error', 'Паролите не съвпадат');
                return false;
            }
        }else {
            $session->addFlashMessage('error', 'Грешен токен');
            return false;
        }
    }
}