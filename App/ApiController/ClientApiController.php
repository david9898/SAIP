<?php


namespace App\ApiController;


use App\Repository\AbonamentRepository;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\NeighborhoodRepository;
use App\Repository\PaymentRepository;
use App\Repository\StreetRepository;
use App\Repository\TownRepository;
use App\Service\AbonamentService;
use App\Service\ClientService;
use App\Service\InvoiceService;
use App\Service\PaymentService;
use Core\Controller\AbstractController;
use Core\Database\PrepareStatementInterface;
use Core\Request\Request;

class ClientApiController extends AbstractController
{
    public function addClient(PrepareStatementInterface $db)
    {
        $this->validateAccess(true);

        $clientRepo       = new ClientRepository($db);
        $streetRepo       = new StreetRepository($db);
        $townRepo         = new TownRepository($db);
        $neighborhoodRepo = new NeighborhoodRepository($db);
        $abonamentRepo    = new AbonamentRepository($db);
        $paymentRepo      = new PaymentRepository($db);
        $invoiceRepo      = new InvoiceRepository($db);
        $clientService    = new ClientService();
        $abonamentService = new AbonamentService();
        $request          = new Request();

        $responce = $clientService->addClient($clientRepo, $streetRepo, $neighborhoodRepo, $townRepo,
                                            $abonamentRepo, $paymentRepo, $invoiceRepo, $abonamentService,
                                            $request->getContent());

        return $this->jsonResponce($responce);
    }

    public function getClients(PrepareStatementInterface $db, $csrfToken, $firstResult)
    {
        $this->validateAccess(true);

        $clientRepo    = new ClientRepository($db);
        $clientService = new ClientService();

        $res = $clientService->getClients($clientRepo, $firstResult, $csrfToken);

        return $this->jsonResponce($res);
    }

    public function getSearchFriends(PrepareStatementInterface $db, $csrfToken, $firstResult, $pattern)
    {
        $this->validateAccess(true);

        $clientRepo    = new ClientRepository($db);
        $clientService = new ClientService();

        $res = $clientService->getSearchFriends($clientRepo, $firstResult, $csrfToken, $pattern);

        return $this->jsonResponce($res);
    }


    public function addPayment($db)
    {
        $this->validateAccess(1);

        $request        = new Request();
        $clientRepo     = new ClientRepository($db);
        $paymentRepo    = new PaymentRepository($db);
        $paymentService = new PaymentService();

        $responce = $paymentService->addPayment($request, $paymentRepo, $clientRepo);

        return $this->jsonResponce($responce);
    }

    public function getIncomeToAccount($db, $abonamentId, $csrfToken)
    {
        $this->validateAccess(1);

        $abonamentRepo    = new AbonamentRepository($db);
        $abonamentService = new AbonamentService();

        $responce = $abonamentService->getIncomeAccount($abonamentRepo, $abonamentId, $csrfToken);

        return $this->jsonResponce($responce);
    }

    public function payInvoices($db)
    {
        $this->validateAccess(1);

        $clientRepo     = new ClientRepository($db);
        $invoiceRepo    = new InvoiceRepository($db);
        $paymentRepo    = new PaymentRepository($db);
        $invoiceService = new InvoiceService();
        $request        = new Request();

        $responce = $invoiceService->payInvoices($invoiceRepo, $paymentRepo, $clientRepo,$request->getContent());

        return $this->jsonResponce($responce);
    }
}