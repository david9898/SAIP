<?php


namespace App\ApiController;


use App\Repository\StaffRepository;
use App\Service\StaffService;
use Core\Controller\AbstractController;
use Core\Exception\ValidationExeption;
use Core\Request\Request;
use Core\Session\Session;
use Core\Validation\Validator;

class StaffApiController extends AbstractController
{
    public function addStaff($db)
    {
        $request         = new Request();
        $staffRepository = new StaffRepository($db);
        $staffService    = new StaffService();

        $res = $staffService->registerStaff($staffRepository, $request->getContent());

        return $this->jsonResponce($res);
    }

    public function getOneCustomer($db, $customerId, $csrfToken)
    {
        $session = new Session();

        if ( $session->get('csrf_token') === $csrfToken ) {
            $staffRepo = new StaffRepository($db);

            $res = $staffRepo->getOneCustomer($customerId);

            return $this->jsonResponce([
                'status' => 'success',
                'staff'  => $res->getApiResponce()
            ]);
        }else {
            return $this->jsonResponce([
                'status' => 'error',
                'description' => 'Грешен токен!'
            ]);
        }
    }

    public function updateStaff($db)
    {
        $request      = new Request();
        $staffRepo    = new StaffRepository($db);
        $staffService = new StaffService();

        $res = $staffService->updateStaff($staffRepo, $request->getContent());

        return $this->jsonResponce($res);
    }

    public function deleteStaff($db)
    {
        try {
            $reqest    = new Request();
            $staffRepo = new StaffRepository($db);
            $session = new Session();
            $parceData = json_decode($reqest->getContent(), true);

            if ( $session->get('csrf_token') === $parceData['csrf_token'] ) {
                $staffId = $parceData['id'];

                Validator::validateInt($staffId);

                if ($staffRepo->disableStaff($staffId)) {
                    return $this->jsonResponce([
                        'status' => 'success',
                        'staffId' => $staffId
                    ]);
                }
            }else {
                return [
                    'status'      => 'error',
                    'description' => 'Грешен токен!'
                ];
            }
        }catch (ValidationExeption $exception) {
            return $this->jsonResponce([
                'status'      => 'error',
                'description' => $exception->getMessage()
            ]);
        }
    }
}