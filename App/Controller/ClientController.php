<?php


namespace App\Controller;

use App\DTO\ClientDTO;
use App\DTO\InvoiceDTO;
use App\Repository\AbonamentRepository;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\PaymentRepository;
use App\Repository\StreetRepository;
use App\Repository\TownRepository;
use App\Service\ClientService;
use App\Service\InvoiceService;
use App\Service\PaymentService;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;
use Core\DataBinder\DataBinder;
use Core\Request\Request;

class ClientController extends AbstractController
{
    public function showClients($db)
    {
        $this->validateAccess(true);

        $csrfToken     = $this->generateCsrfToken();

        $this->render('Clients/clientsPaginationTemplate.php', [
            'css' => [
                'styles/clients.css',
                'styles/header.css'
            ],
            'js'  => [
                'node_modules/handlebars/dist/handlebars.min.js',
                'scripts/clients.js'
            ],
            'csrf_token' => $csrfToken
        ]);
    }

    public function addClient(PrepareStatementInterface $db)
    {
        $this->validateAccess(true);

        $townRepo      = new TownRepository($db);
        $abonamentRepo = new AbonamentRepository($db);

        $towns      = $townRepo->getTowns();
        $abonaments = $abonamentRepo->getAbonaments();
        $csrf_token = $this->generateCsrfToken();

        $this->render('Clients/addClient.php', [
            'css'        => [
                'node_modules/easy-autocomplete/dist/easy-autocomplete.min.css',
                'styles/header.css',
                'styles/addClient.css',
            ],
            'js'         => [
                'node_modules/easy-autocomplete/dist/jquery.easy-autocomplete.min.js',
                'node_modules/sweetalert/dist/sweetalert.min.js',
                'scripts/addClient.js'
            ],
            'towns'      => $towns,
            'abonaments' => $abonaments,
            'csrf_token' => $csrf_token
        ]);
    }

    public function seeClient(PrepareStatementInterface $db, $id)
    {
        $this->validateAccess(1);

        $clientRepo     = new ClientRepository($db);
        $paymentRepo    = new PaymentRepository($db);
        $invoiceRepo    = new InvoiceRepository($db);
        $clientService  = new ClientService();
        $paymentService = new PaymentService();
        $invoiceService = new InvoiceService();
        $client         = $clientRepo->getClient($id);
        $csrfToken      = $this->generateCsrfToken();


        $this->render('Clients/clientTemplate.php', [
            'css'        => [
                'styles/header.css',
                'styles/addStaff.css',
                'styles/client.css'
            ],
            'js'         => [
                'node_modules/moment/min/moment-with-locales.min.js',
                'node_modules/handlebars/dist/handlebars.min.js',
                'node_modules/sweetalert/dist/sweetalert.min.js',
                'node_modules/print-this/printThis.js',
                'scripts/client.js'
            ],
            'client'      => $client,
            'invoices'    => $invoiceRepo->getInvoicesOnClient($id),
            'payments'    => $paymentRepo->getClientPayments($id),
            'lastInvoice' => date('Y-n', $invoiceRepo->getLastInvoiceOnClient($id)->getEnd()),
            'csrf_token'  => $csrfToken
        ]);
    }

    public function getOldClients($db)
    {
        $clientRepo = new ClientRepository($db);

        $this->baseRender('Clients/showOldClients.php', [
            'oldClients' => $clientRepo->getFromOld()
        ]);
    }

    public function editOldClient($db, $id)
    {
        $request     = new Request();
        $clientRepo  = new ClientRepository($db);
        $streetRepo  = new StreetRepository($db);
        $invoiceRepo = new InvoiceRepository($db);

        if ( $request->isSubmit('add_new_client') ) {
            $clientDTO = new ClientDTO();
            /** @var ClientDTO $client */
            $client    = DataBinder::bindData($request->getPOST(), $clientDTO);
            $stretId   = $streetRepo->getStreetByName($client->getStreet())->getId();
            $client->setStreet($stretId);
            $clientId  = $clientRepo->addClient($client);

            /** INVOICE CODE */
            $oldData        = $clientRepo->getCertainOld($id);
            $monthYearStart = explode('-', $oldData->getLastInvoicePaid());
            $monthYearEnd   = explode('-', $oldData->getStopService());
            $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $monthYearEnd[1], $monthYearEnd[2]);
//            $startInvoice   = strtotime('01' . '-' . $monthYearStart[1] . '-' . $monthYearStart[2] . ' ' . 'midnight');
            $endInvoice     = strtotime($monthYearEnd[2] . '-' . $monthYearEnd[1] . '-' . $daysInMonth . ' ' . '23:59:59');
            $lastPaymentSec = strtotime($oldData->getLastInvoicePaid() . ' 12:00:00');

            $firstInvoice   = new InvoiceDTO();
//            $firstInvoice->setTime($startInvoice);
            $firstInvoice->setClient($clientId);
            $firstInvoice->setSum(30);
//            $firstInvoice->setStart($startInvoice);
            $firstInvoice->setTimePaid($lastPaymentSec);
            $firstInvoice->setEnd($endInvoice);
            $firstInvoice->setOperator(1);
            $invoiceRepo->addInvoice($firstInvoice);

            $clientRepo->disableOldClient($id);

            if ( time() > $endInvoice ) {
                $currentYear      = date('Y');
                $currentMonth     = date('m')[1];
                $lastInvoiceYear  = $monthYearEnd[2];
                $lastInvoiceMonth = $monthYearEnd[1];

                if ( $lastInvoiceYear === $currentYear ) {
                    $numInvoices = $currentMonth - $lastInvoiceMonth;

                    for ($i = 1; $i <= $numInvoices; $i++) {
                        $lastInvoiceTimeEnd   = $invoiceRepo->getLastInvoiceOnClient($clientId)->getEnd();
                        $realLastInvoiceMonth = date('n', $lastInvoiceTimeEnd);
                        $realLastInvoiceMonth++;
                        $realDaysInMonth      = cal_days_in_month(CAL_GREGORIAN, '0' . $realLastInvoiceMonth, 2019);

                        $start                = strtotime('01-' . $realLastInvoiceMonth . '-2019 00:00:00');
                        $end                  = strtotime($realDaysInMonth . '-' . $realLastInvoiceMonth . '-2019 23:59:59');

                        $newInvoice = new InvoiceDTO();
                        $newInvoice->setTime($start);
                        $newInvoice->setStart($start);
                        $newInvoice->setEnd($end);
                        $newInvoice->setSum(30);
                        $newInvoice->setClient($clientId);

                        $invoiceRepo->addInvoice($newInvoice);
                    }

                }else {
                    $diffYears = (int)$currentYear - (int)$lastInvoiceYear;

                    if ( $diffYears === 1 ) {
                        $lastYearMonths = 12 - $lastInvoiceMonth;
                        $numInvoices    = (int)$currentMonth + (int)$lastYearMonths;

                        for ($i = 1; $i <= $numInvoices; $i++) {
                            $lastInvoiceTimeEnd     = $invoiceRepo->getLastInvoiceOnClient($clientId)->getEnd();
                            $lastInvoiceDate        = date('n-Y', $lastInvoiceTimeEnd);
                            $explodeLastInvoiceDate = explode('-', $lastInvoiceDate);
                            $realLastInvoiceMonth   = (int)$explodeLastInvoiceDate[0];
                            $realLastInvoiceYear    = (int)$explodeLastInvoiceDate[1];
                            $realLastInvoiceMonth++;

                            if ( $realLastInvoiceMonth > 12 ) {
                                $realLastInvoiceYear++;
                                $realLastInvoiceMonth = 1;
                                $realDaysInMonth   = cal_days_in_month(CAL_GREGORIAN, '0' . $realLastInvoiceMonth, $realLastInvoiceYear);

                                $start                = strtotime('01' . '-' . $realLastInvoiceMonth . '-2019 00:00:00');
                                $end                  = strtotime($realDaysInMonth . '-' . $realLastInvoiceMonth . '-2019 23:59:59');

                                $newInvoice = new InvoiceDTO();
                                $newInvoice->setTime($start);
                                $newInvoice->setStart($start);
                                $newInvoice->setEnd($end);
                                $newInvoice->setSum(30);
                                $newInvoice->setClient($clientId);

                                $invoiceRepo->addInvoice($newInvoice);
                            }else {
                                $realDaysInMonth   = cal_days_in_month(CAL_GREGORIAN, '0' . $realLastInvoiceMonth, $realLastInvoiceYear);

                                $start                = strtotime('01-' . $realLastInvoiceMonth . '-2019 00:00:00');
                                $end                  = strtotime($realDaysInMonth . '-' . $realLastInvoiceMonth . '-2019 23:59:59');

                                $newInvoice = new InvoiceDTO();
                                $newInvoice->setTime($start);
                                $newInvoice->setStart($start);
                                $newInvoice->setEnd($end);
                                $newInvoice->setSum(30);
                                $newInvoice->setClient($clientId);

                                $invoiceRepo->addInvoice($newInvoice);
                            }
                        }

                    }
                }
            }

            $this->redirect('/showOldClients');
        }else {
            $csrfToken = $this->generateCsrfToken();

            $this->baseRender('Clients/editOldClients.php', [
                'oldClient'  => $clientRepo->getCertainOld($id),
                'csrf_token' => $csrfToken
            ]);
        }
    }
}