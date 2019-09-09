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

    public function registerStaff(StaffRepositoryInterface $staffRepo, $postArr): array
    {
        try {
            $session = new Session();
            $post    = json_decode($postArr, true);

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
                        $staffRepo->addRole(
                            $staffRepo->getCustomer($customer->getUsername())->getId(),
                            $role
                        );
                    }

                    $session->addFlashMessage('success', 'Успешно добавихте нов служител');

                    return [
                        'status' => 'success'
                    ];
                } else {
                    return [
                        'status'      => 'error',
                        'description' => 'Паролите не съвпадат'
                    ];
                }
            } else {
                return [
                    'status'      => 'error',
                    'description' => 'Грешен токен!'
                ];
            }
        }catch (ValidationExeption $exception) {
            return [
                'status'      => 'error',
                'description' => $exception->getMessage()
            ];
        }
    }

    public function updateStaff(StaffRepositoryInterface $staffRepo, $postArr): array
    {
        try {
            $session = new Session();
            $postArr = json_decode($postArr, true);

            if ($session->get('csrf_token') !== $postArr['csrf_token']) {
                return [
                    'status' => 'error',
                    'description' => 'Грешен токен!'
                ];
            }

            $staffDTO = new StaffDTO();
            /** @var StaffDTO $staff */
            $staff = DataBinder::bindData($postArr, $staffDTO);

            Validator::validatePhone($staff->getPhone());
            Validator::validateBgCharacters($staff->getFirstName());
            Validator::validateBgCharacters($staff->getLastName());

            if ($staff->getPassword() !== '') {
                if ($postArr['password'] !== $postArr['repeat_password']) {
                    return [
                        'status' => 'error',
                        'description' => 'Паролите не съвпадат!'
                    ];
                }

                $hashPass = hash('sha512', $staff->getPassword());
                $staff->setPassword($hashPass);

                try {
                    $staffRepo->beginTransaction();

                    $staffRepo->deleteRoles($staff->getId());
                    $staffRepo->updateCustomer($staff);

                    foreach ($staff->getRoles() as $role) {
                        Validator::validateInt($role);
                        $staffRepo->addRole($staff->getId(), $role);
                    }

                    $res = $staffRepo->commit();

                }catch (\Exception $exception) {
                    $staffRepo->rollBack();

                    return [
                        'status' => 'error',
                        'description' => 'Нещо се обърка!'
                    ];
                }

                if ($res) {
                    $session->addFlashMessage('success', 'Успешно променихте служителя!');

                    return [
                        'status' => 'success'
                    ];
                }
            } else {
                $staffPass = $staffRepo->getStaffPass($staff->getId())->getPassword();
                $staff->setPassword($staffPass);

                try {

                    $staffRepo->beginTransaction();

                    $staffRepo->deleteRoles($staff->getId());
                    $staffRepo->updateCustomer($staff);

                    foreach ($staff->getRoles() as $role) {
                        Validator::validateInt($role);
                        $staffRepo->addRole($staff->getId(), $role);
                    }

                    $res = $staffRepo->commit();

                } catch (\Exception $exception) {
                    $staffRepo->rollBack();

                    return [
                        'status' => 'error',
                        'description' => 'Нещо се обърка!'
                    ];
                }

                if ($res) {
                    $session->addFlashMessage('success', 'Успешно променихте служителя!');

                    return [
                        'status' => 'success'
                    ];
                }
            }

        }
        catch (ValidationExeption $exception) {
            $staffRepo->rollBack();

            return [
                'status'      => 'error',
                'description' => $exception->getMessage()
            ];
        }
    }

}