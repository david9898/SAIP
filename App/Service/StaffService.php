<?php


namespace App\Service;

use App\DTO\StaffDTO;
use App\Repository\StaffRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Exception\ValidationExeption;
use Core\Session\Session;
use Core\Validation\Validator;

class StaffService implements StaffServiceInterface
{

    public function login(StaffRepositoryInterface $staffRepo, array $post): bool
    {
        $session      = new Session();

        if ( $post['csrf_token'] !== $session->get('csrf_token') ) {
            $session->addFlashMessage('error', 'Грешен токен');
            return false;
        }

        $staff = new StaffDTO();

        /** @var StaffDTO $unCheckStaff */
        $unCheckStaff = DataBinder::bindData($post, $staff);

        /** @var StaffDTO $customer */
        $staff   = $staffRepo->getCustomer($unCheckStaff->getUsername());

        if ( $staff === null ) {
            $session->addFlashMessage('error', 'Грешно потребителско име!');
            return false;
        }

        $pass = hash('sha512', $post['password']);

        if ( $pass === $staff->getPassword() ) {

            $roles = $staffRepo->getCustomerRoles($staff->getId());

            foreach ($roles as $role) {
                $staff->addRole($role['role']);
            }

            $session->addFlashMessage('success', 'Добре дошли!');
            $userData = [
                'id'       => $staff->getId(),
                'username' => $staff->getUsername(),
                'roles'    => $staff->getRoles()
            ];

            $session->set('userData', $userData);

            return true;
        }

        $session->addFlashMessage('error', 'Грешна парола!');

        return false;
    }

    public function registerStaff(StaffRepositoryInterface $staffRepo, array $post): bool
    {
        try {
            $session = new Session();

            if ($session->get('csrf_token') === $post['csrf_token']) {
                if ($post['password'] === $post['repeat_password']) {

                    $customerData = new StaffDTO();
                    /** @var StaffDTO $customer */
                    $customer = DataBinder::bindData($post, $customerData);

                    $newPass = hash('sha512', $customer->getPassword());
                    $customer->setPassword($newPass);

                    Validator::validatePhone($customer->getPhone());
                    Validator::validateBgCharacters($customer->getFirstName());
                    Validator::validateBgCharacters($customer->getLastName());

                    $customer->setUsername(htmlspecialchars($customer->getUsername()));
                    $customer->setPassword(htmlspecialchars($customer->getPassword()));

                    $staffRepo->addCustomer($customer);

                    foreach ($customer->getRoles() as $role) {
                        Validator::validateInt($role);
                        $staffRepo->addRole($staffRepo->getCustomer($customer->getUsername())->getId(), $role);
                    }

                    $session->addFlashMessage('success', 'Успешно добавихте нов служител');

                    return true;
                } else {
                    $session->addFlashMessage('error', 'Паролите не съвпадат');
                    return false;
                }
            } else {
                $session->addFlashMessage('error', 'Грешен токен');
                return false;
            }
        }catch (ValidationExeption $exception) {
            $session->addFlashMessage('error', $exception->getMessage());
            return false;
        }
    }
}