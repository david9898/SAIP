<?php


namespace App\Service;


use App\DTO\ClientDTO;
use App\DTO\PaymentDTO;
use App\Repository\ClientRepositoryInterface;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use App\Repository\TownRepositoryInterface;
use Core\DataBinder\DataBinder;
use Core\Exception\ValidationExeption;
use Core\Request\Request;
use Core\Session\Session;
use Core\Validation\Validator;

class ClientService implements ClientServiceInterface
{
    public function addClient(ClientRepositoryInterface $clientRepo, StreetRepositoryInterface $streetRepo,
                              NeighborhoodRepositoryInterface $neighborhoodRepo, TownRepositoryInterface $townRepo,
                              array $post): array
    {
        try {
            $session = new Session();

            $realCsrfToken = $session->get('csrf_token');

            if ( $post['csrf_token'] === $realCsrfToken ) {
                $clientData = new ClientDTO();
                /** @var ClientDTO $client */
                $client = DataBinder::bindData($post, $clientData);

                $streetId = $streetRepo->getStreetByName($client->getStreet());

                if ( $streetId === null ) {
                    return ['status' => 'error', 'description' => 'Улицата не съществува'];
                }

                if ( $townRepo->checkForStreetInTown($client->getTown(), $streetId->getId()) === false ) {
                    return ['status' => 'error', 'description' => 'Улицата и града не съвпадат'];
                }

                $client->setStreet($streetId->getId());

                $isHaveNeighborhood = $neighborhoodRepo->checkTownHaveNeighborhood($client->getTown());

                if ( $isHaveNeighborhood === true ) {
                    if ($isHaveNeighborhood === true && $client->getNeighborhood() === null) {
                        return ['status' => 'error', 'description' => 'Трябва да зададете квартал'];
                    }

                    $neighborhoodId = $neighborhoodRepo->getNeighborhoodId($client->getNeighborhood());

                    if ($neighborhoodId === null) {
                        return ['status' => 'error', 'description' => 'Квартала не е валиден'];
                    }

                    if ($neighborhoodRepo->checkForValidNeighborhood($client->getTown(), $neighborhoodId) === false) {
                        return ['status' => 'error', 'description' => 'Квартала не е валиден'];
                    }

                    $client->setNeighborhood($neighborhoodId);
                }else {
                    $client->setNeighborhood(null);
                }

                if ( $client->getDescription() === '' ) {
                    $client->setDescription(null);
                }

                Validator::validateInt($client->getStreetNumber());
                Validator::validateInt($client->getAbonament());
                Validator::validateInt($client->getDateRegister());
                Validator::validateInt($client->getTown());
                Validator::validateInt($client->getStreet());
                Validator::validateBgCharacters($client->getFirstName());
                Validator::validateBgCharacters($client->getLastName());
                Validator::validateEmail($client->getEmail());
                Validator::validatePhone($client->getPhone());

                if ( $client->getDescription() !== null ) {
                    Validator::validateBgCharacters($client->getDescription());
                }

                if ( $client->getNeighborhood() !== null ) {
                    Validator::validateInt($client->getNeighborhood());
                }

                if ( $clientRepo->addClient($client) ) {
                    $session->addFlashMessage('success', 'Успешно добавихте нов клиент');
                    return ['status' => 'success'];
                }
            } else {
                return ['status' => 'error', 'description' => 'Грешен токен!!!'];
            }
        }catch (ValidationExeption $e) {
            return ['status' => 'error', 'description' => $e->getMessage()];
        }
    }



    public function getClients(ClientRepositoryInterface $repository): array
    {
        $time      = time();
        $res       = [];
        $generator = $repository->getClients();

        foreach ($generator as $client) {
            /** @var ClientDTO $client */

            if ( $client->getPaid() === null ) {
                $res[] = $client;
                continue;
            }

            $diff = $client->getPaid() - $time;

            if ( $diff < 0 ) {
                $daysDiff = floor($diff / 86400);
                $client->setPaid($daysDiff);
                $res[] = $client;
                continue;
            }else {
                $daysDiff = ceil($diff / 86400);
                $client->setPaid($daysDiff);
                $res[] = $client;
                continue;
            }
        }

        return $res;
    }



    public function addPayment(Request $request, PaymentRepositoryInterface $paymentRepo,
                                ClientRepositoryInterface $clientRepo): array
    {
        try {
            $postArr = json_decode($request->getContent(), true);
            $session = new Session();

            if ($session->get('csrf_token') === $postArr['csrf_token']) {
                $clientAbonamentPrice = $clientRepo->getClientAbonamentPrice($postArr['client'])->getSum();

                for ($i = 0; $i < $postArr['bills']; $i++) {
                    $paymentDTO = new PaymentDTO();

                    /** @var PaymentDTO $payment */
                    $payment = DataBinder::bindData($postArr, $paymentDTO);

                    $lastPayment = $paymentRepo->getLastPayment($payment->getClient());

                    if ($lastPayment !== null) {
                        $lastPayment = $lastPayment->getEndTime();

                        if ((time() - $lastPayment) > 7905600) {
                            $endTime = $lastPayment + (time() - ($lastPayment + 7905600));
                            $payment->setStartTime($lastPayment);
                            $payment->setEndTime($endTime + 2635200);
                        } else {
                            $payment->setStartTime($lastPayment);
                            $payment->setEndTime($lastPayment + 2635200);
                        }

                    } else {
                        $payment->setStartTime(time());

                        $payment->setEndTime(time() + 2635200);
                    }

                    $payment->setSum($clientAbonamentPrice);
                    $payment->setOperator($session->get('userData')['id']);

                    Validator::validateInt($payment->getTime());
                    Validator::validateInt($payment->getStartTime());
                    Validator::validateInt($payment->getEndTime());
                    Validator::validateInt($payment->getClient());
                    Validator::validateInt($payment->getOperator());
                    Validator::validateInt($payment->getSum());

                    $paymentRepo->addPayment($payment);
                }

                return ['status' => 'success'];
            } else {
                return ['status' => 'error', 'description' => 'Грешен токен'];
            }
        }catch (ValidationExeption $exception) {
            return ['status' => 'error', 'description' => $exception->getMessage()];
        }
    }

    public function calculateBills(PaymentRepositoryInterface $paymentRepo, $id): array
    {
        $lastThreePayments       = $paymentRepo->getLastThreePayments($id);

        $checkIfReadablePayments = $this->checkIfPaymentsAreReadable($lastThreePayments);

        if ( $checkIfReadablePayments === null || $checkIfReadablePayments === true ) {

            return $this->calculateBillsIfTrueOrNull($paymentRepo, $id);

        }else {

            return $this->calculateBillsIfFalse($paymentRepo, $id);

        }


    }

    public function checkIfPaymentsAreReadable(?\Generator $payments): ?bool
    {
        if ( $payments === null ) {
            return null;
        }

        foreach ($payments as $payment) {
            /** @var PaymentDTO $payment */
            $diff = $payment->getEndTime() - $payment->getStartTime();

            if ( $diff > 2635200 ) {
                return false;
            }
        }

        return true;
    }

    public function calculateBillsIfTrueOrNull(PaymentRepositoryInterface $paymentRepo, $id): array
    {
        $lastPayment    = $paymentRepo->getLastPayment($id);
        $time           = time();

        if ( $lastPayment !== null ) {
            $lastTime = $lastPayment->getEndTime();
        }else {
            $lastTime = null;
        }

        if ( $lastPayment !== null ) {
            if ( $lastTime > $time ) {
                return [
                    'delay'    => 'no',
                    'paid'     => $lastTime,
                    'lastTime' => $lastTime,
                    'bills'    => []
                ];
            }else {
                $diffTime = time() - $lastTime;
                $numBills = ceil($diffTime / 2635200);

                if ( (int)$numBills === 1 ) {
                    $bills = [
                        ['start' => $lastTime, 'end' => $lastTime + 2635200]
                    ];
                }
                else if ( (int)$numBills === 2 ) {
                    $bills = [
                        ['start' => $lastTime, 'end' => $lastTime + 2635200],
                        ['start' => $lastTime + 2635200, 'end' => $lastTime + 5270400]
                    ];
                }else{
                    $bills = [
                        ['start' => $lastTime, 'end' => $lastTime + 2635200],
                        ['start' => $lastTime + 2635200, 'end' => $lastTime + 5270400],
                        ['start' => $lastTime + 5270400, 'end' => $lastTime + 7905600]
                    ];
                }
                return [
                    'delay'    => 'yes',
                    'bills'    => $bills,
                    'lastTime' => $lastTime
                ];
            }
        }else {
            return [
                'delay'    => 'none',
                'lastTime' => time(),
                'bills'    => []
            ];
        }
    }

    public function calculateBillsIfFalse(PaymentRepositoryInterface $paymentRepo, $id): array
    {
        $lastPayment       = $paymentRepo->getLastPayment($id);
        $lastTime          = $lastPayment->getEndTime();
        $time              = time();
        $lastThreePayments = $paymentRepo->getLastThreePayments($id);

        if ( $lastTime > $time ) {
            return [
                'delay'    => 'no',
                'paid'     => $lastTime,
                'lastTime' => $lastTime,
                'bills'    => []
            ];
        }else {
            $diffTime = time() - $lastTime;
            $numBills = ceil($diffTime / 2635200);

            if ( (int)$numBills === 1 ) {
                $bills = [
                    ['start' => $lastTime, 'end' => $lastTime + 2635200]
                ];
            }
            else if ( (int)$numBills === 2 ) {
                $bills = [
                    ['start' => $lastTime, 'end' => $lastTime + 2635200],
                    ['start' => $lastTime + 2635200, 'end' => $lastTime + 5270400]
                ];
            }else{
                $bills = [
                    ['start' => $lastTime, 'end' => $lastTime + 2635200],
                    ['start' => $lastTime + 2635200, 'end' => $lastTime + 5270400],
                    ['start' => $lastTime + 5270400, 'end' => $lastTime + 7905600]
                ];
            }
            return [
                'delay'    => 'yes',
                'bills'    => $this->makeBillsReadable($bills, $lastThreePayments),
                'lastTime' => $lastTime
            ];
        }
    }

    public function makeBillsReadable(array $bills, $lastThreePayments): array
    {
        /** @var PaymentDTO[] $lastPayments */
        $lastPayments = [];

        foreach ($lastThreePayments as $payment) {
            $lastPayments[] = $payment;
        }

        $lastPayments = array_reverse($lastPayments);

        for ($i = 0;$i < count($lastPayments);$i++) {
            /** @var PaymentDTO $currPayment */
            $currPayment = $lastPayments[$i];

            $diff = $currPayment->getEndTime() - $currPayment->getStartTime();
            if ( $diff > 2635200 ) {

                if ( count($lastPayments) === 3 ) {

                    if ($i === 1) {

                        if (isset($bills[0])) {
                            $bills[0]['start'] = $bills[0]['start'] - $diff + 2635200;
                            $bills[0]['end']   = $bills[0]['end'] - $diff + 2635200;
                        }


                    }

                    if ($i === 2) {

                        if (isset($bills[0])) {
                            $bills[0]['start'] = $bills[0]['start'] - $diff + 2635200;
                            $bills[0]['end']   = $bills[0]['end'] - $diff + 2635200;
                        }

                        if (isset($bills[1])) {
                            $bills[1]['start'] = $bills[1]['start'] - $diff + 2635200;
                            $bills[1]['end']   = $bills[1]['end'] - $diff + 2635200;
                        }

                    }
                }

                if ( count($lastPayments) === 2 ) {
                    if ($i === 0) {
                        if (isset($bills[0])) {
                            $bills[0]['start'] = $bills[0]['start'] - $diff + 2635200;
                            $bills[0]['end']   = $bills[0]['end'] - $diff + 2635200;
                        }

                    }

                    if ($i === 1) {

                        if (isset($bills[0])) {
                            $bills[0]['start'] = $bills[0]['start'] - $diff + 2635200;
                            $bills[0]['end']   = $bills[0]['end'] - $diff + 2635200;
                        }

                        if (isset($bills[1])) {
                            $bills[1]['start'] = $bills[1]['start'] - $diff + 2635200;
                            $bills[1]['end']   = $bills[1]['end'] - $diff + 2635200;
                        }

                    }

                    if ( count($lastPayments) === 1 ) {

                        if (isset($bills[0])) {
                            $bills[0]['start'] = $bills[0]['start'] - $diff + 2635200;
                            $bills[0]['end']   = $bills[0]['end'] - $diff + 2635200;
                        }

                        if (isset($bills[1])) {
                            $bills[1]['start'] = $bills[1]['start'] - $diff + 2635200;
                            $bills[1]['end']   = $bills[1]['end'] - $diff + 2635200;
                        }

                    }
                }
            }
        }

        return $bills;
    }

}