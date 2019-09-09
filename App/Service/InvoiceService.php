<?php


namespace App\Service;


use App\DTO\ClientDTO;
use App\DTO\InvoiceDTO;
use App\DTO\PaymentDTO;
use App\Repository\ClientRepositoryInterface;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use Core\Exception\ValidationExeption;
use Core\Session\Session;
use Core\Validation\Validator;

class InvoiceService implements InvoiceServiceInterface
{
    public function addInvoicesToAllClients(InvoiceRepositoryInterface $invoiceRepository, ClientRepositoryInterface $clientRepository): bool
    {
        /** @var ClientDTO[] $clients */
        $clients = $clientRepository->getClientsIdsAndSums();
        $time    = time();
        $end     = date('Y-m-') . date('t');
        $end     = strtotime($end);
        $end     = (int)$end + 86399;

        foreach ($clients as $client) {
            $invoice = new InvoiceDTO();

            $invoice->setStart($time);
            $invoice->setEnd($end);
            $invoice->setSum($client->getSum());
            $invoice->setTime($time);
            $invoice->setClient($client->getId());

            $invoiceRepository->addInvoice($invoice);
        }

        return true;
    }

    public function generateInvoices($unPaidInvoices, $numInvoices, $clientId, InvoiceRepositoryInterface $invoiceRepository,
                                     $clientAbonamentPrice): bool
    {
        $time                   = time();
        $unPaidInvoicesOnClient = [];
        if ( $unPaidInvoices !== null ) {
            foreach ($unPaidInvoices as $unPaidInvoice) {
                $unPaidInvoicesOnClient[] = $unPaidInvoice;
            }
        }

        if ( count($unPaidInvoicesOnClient) < $numInvoices ) {
            $numInvoices = $numInvoices - count($unPaidInvoicesOnClient);
            $lastInvoice = $invoiceRepository->getLastInvoiceOnClient($clientId)->getEnd();
            $date = date('Y-n', $lastInvoice);
            $explodeDate = explode('-', $date);

            for ($i = 1;$i <= $numInvoices;$i++) {
                if ( $explodeDate[1] < 12 ) {
                    $explodeDate[1] = (int)$explodeDate[1] + 1;
                }else {
                    $explodeDate[0] = (int)$explodeDate[0] + 1;
                    $explodeDate[1] = 1;
                }

                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $explodeDate[1], $explodeDate[0]);

                $start       = strtotime(date($explodeDate[0] . '-' .$explodeDate[1] . '-01 00:00:00'));
                $end         = strtotime(date($explodeDate[0] . '-' .$explodeDate[1] . '-' . $daysInMonth . ' 23:59:59'));

                $invoice = new InvoiceDTO();
                $invoice->setTime($time);
                $invoice->setClient($clientId);
                $invoice->setStart($start);
                $invoice->setEnd($end);
                $invoice->setSum($clientAbonamentPrice);

                $invoiceRepository->addInvoice($invoice);
            }

            return true;
        }else {
            return false;
        }
    }

    public function payInvoices(InvoiceRepositoryInterface $invoiceRepository, PaymentRepositoryInterface $paymentRepository,
                                ClientRepositoryInterface $clientRepository, $postArr): array
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

            Validator::validateInt($postArr['clientId']);
            Validator::validateInt($postArr['numInvoices']);
            
            $clientId = $postArr['clientId'];
            $unPaidInvoices = $invoiceRepository->getUnpaidInvoicesOnClient($clientId);
            $time = time();
            $clientAbonamentPrice = $clientRepository->getClientAbonamentPrice($clientId)->getSum();
            $needSum = $clientAbonamentPrice * $postArr['numInvoices'];


            $operator = $session->get('userData')['id'];

            $invoiceGenerator = $this->generateInvoices($unPaidInvoices, $postArr['numInvoices'], $clientId,
                $invoiceRepository, $clientAbonamentPrice);

            if ($invoiceGenerator) {
                $invoiceRepository->payAllInvoices($clientId, $operator, $time);

                return [
                    'status' => 'success',
                    'numInvoices' => $postArr['numInvoices'],
                    'time' => time(),
                ];
            } else {
                $unPaidInvoices = $invoiceRepository->getUnpaidInvoicesOnClient($clientId);

                /** @var InvoiceDTO[] $unPaidInvoicesArr */
                $unPaidInvoicesArr = [];
                foreach ($unPaidInvoices as $unPaidInvoice) {
                    $unPaidInvoicesArr[] = $unPaidInvoice;
                }

                for ($i = 0; $i < $postArr['numInvoices']; $i++) {
                    $invoiceId = $unPaidInvoicesArr[$i]->getId();

                    $invoiceRepository->payInvoice($invoiceId, $operator, $time);

                }

                $payment = new PaymentDTO();
                $payment->setSum($needSum);
                $payment->setClient($clientId);
                $payment->setTime($time);
                $payment->setOperator($operator);
                $paymentRepository->addPayment($payment);

                return [
                    'status' => 'success',
                    'numInvoices' => $postArr['numInvoices'],
                    'time' => time(),
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