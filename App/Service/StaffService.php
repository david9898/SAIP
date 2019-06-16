<?php


namespace App\Service;

use App\DTO\StaffDTO;
use App\Repository\StaffRepositoryInterface;
use Core\Session\Session;

class StaffService implements StaffServiceInterface
{

    public function login(StaffRepositoryInterface $staffRepo, array $post): bool
    {
        $session      = new Session();

        /** @var StaffDTO $customer */
        $customer   = $staffRepo->getCustomer($post['username']);

        if ( $customer === null ) {
            $session->addFlashMessage('error', 'Грешно потребителско име');
            return false;
        }

        $pass = hash('sha512', $post['password']);

        if ( $pass === $customer->getPassword() ) {
            $session->addFlashMessage('success', 'Добре дошли');
            $userData = [
                'id'       => $customer->getId(),
                'username' => $customer->getUsername(),
                'role'     => $customer->getRole()
            ];

            $session->set('userData', $userData);

            return true;
        }

        $session->addFlashMessage('error', 'Грешна парола');

        return false;
    }
}