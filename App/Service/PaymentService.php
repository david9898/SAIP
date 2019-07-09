<?php


namespace App\Service;


use App\DTO\PaymentDTO;
use App\Repository\PaymentRepositoryInterface;

class PaymentService implements PaymentServiceInterface
{
    public function getClientPayments(PaymentRepositoryInterface $paymentRepository, $clientId): ?array
    {
        $payments = $paymentRepository->getClientPayments($clientId);

        if ( $payments !== null ) {
            $arrPayments = [];

            foreach ($payments as $payment) {
                $arrPayments[] = $payment;
            }

            return $this->makePaymentsReadable($arrPayments);

        }else {
            return null;
        }
    }

    public function makePaymentsReadable($payments): array
    {
        $readablePaymetns = [];

        for ($i = 0;$i < count($payments);$i++) {
            /** @var PaymentDTO $payment */
            $payment = $payments[$i];

            $readablePaymetns[] = $payment;

            $diffStartEndPayment = $payment->getEndTime() - $payment->getStartTime();

            if ( $diffStartEndPayment > 2635200 ) {
                $diffTime = $diffStartEndPayment - 2635200;

                $readablePaymetns[$i]->setEndTime($payment->getEndTime() - $diffTime);

                if ( isset($payments[$i + 1]) ) {

                    $payments[$i + 1]->setStartTime($payments[$i + 1]->getStartTime() - $diffTime);
                    $payments[$i + 1]->setEndTime($payments[$i + 1]->getEndTime() - $diffTime);
                }

                if ( isset($payments[$i + 2]) ) {
                    $payments[$i + 2]->setStartTime($payments[$i + 2]->getStartTime() - $diffTime);
                    $payments[$i + 2]->setEndTime($payments[$i + 2]->getEndTime() - $diffTime);
                }
            }else {
                continue;
            }
        }

        return $readablePaymetns;
    }
}