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

                $isAdd = $clientRepo->addClient($client);

                if ( $isAdd ) {
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

                    if ( (time() - $lastPayment) > 7905600 ) {
                        $endTime = $lastPayment + (time() - ($lastPayment + 7905600));
                        $payment->setStartTime($lastPayment);
                        $payment->setEndTime($endTime + 2635200);
                    }else {
                        $payment->setStartTime($lastPayment);
                        $payment->setEndTime($lastPayment + 2635200);
                    }

                } else {
                    $payment->setStartTime(time());

                    $payment->setEndTime(time() + 2635200);
                }

                $payment->setSum($clientAbonamentPrice);
                $payment->setOperator($session->get('userData')['id']);

                $paymentRepo->addPayment($payment);
            }

            return ['status' => 'success'];
        }else {
            return ['status' => 'error', 'description' => 'Грешен токен'];
        }
    }

    public function calculateBills($lastPayment, $lastTime): array
    {
        if ( $lastPayment !== null ) {
            if ( $lastTime > time() ) {
                $time = [
                    'delay'    => 'no',
                    'paid'     => date('Y:m:d', $lastTime),
                    'lastTime' => $lastTime
                ];
            }else {
                $diffTime = time() - $lastTime;
                $numBills    = ceil($diffTime / 2635200);

                if ( (int)$numBills === 1 ) {
                    $bills = [
                        ['start' => date('Y:m:d', $lastTime), 'end' => date('Y:m:d', $lastTime + 2635200)]
                    ];
                }
                else if ( (int)$numBills === 2 ) {
                    $bills = [
                        ['start' => date('Y:m:d', $lastTime), 'end' => date('Y:m:d', $lastTime + 2635200)],
                        ['start' => date('Y:m:d', $lastTime + 2635200), 'end' => date('Y:m:d', $lastTime + 5270400)]
                    ];
                }else{
                    $bills = [
                        ['start' => date('Y:m:d', $lastTime), 'end' => date('Y:m:d', $lastTime + 2635200)],
                        ['start' => date('Y:m:d', $lastTime + 2635200), 'end' => date('Y:m:d', $lastTime + 5270400)],
                        ['start' => date('Y:m:d', $lastTime + 5270400), 'end' => date('Y:m:d', $lastTime + 7905600)]
                    ];
                }
                $time = [
                    'delay'    => 'yes',
                    'bills'    => $bills,
                    'lastTime' => $lastTime
                ];
            }
            return $time;
        }else {
            return [
                'delay'    => 'none',
                'lastTime' => 'none'
            ];
        }

    }

}