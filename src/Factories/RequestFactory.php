<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Factories;

use Vanilo\Netopia\Concerns\InteractsWithNetopia;
use Vanilo\Netopia\Messages\NetopiaPaymentRequest;
use Vanilo\Payment\Contracts\Payment;

final class RequestFactory
{
    use InteractsWithNetopia;

    public function create(Payment $payment, array $options = []): NetopiaPaymentRequest
    {
        $result = new NetopiaPaymentRequest(
            $this->signature,
            $this->publicCertificatePath,
            $this->privateCertificatePath,
            $this->isSandbox
        );
        $billPayer = $payment->getPayable()->getBillPayer();

        $result
            ->setPaymentId($payment->getPaymentId())
            ->setCurrency($payment->getCurrency())
            ->setAmount($payment->getAmount())
            ->setTimestamp(date('YmdHis'))
            ->setDetails($options['description'] ?? __('Order no. :number', ['number' => $payment->getPayable()->getTitle()]))
            ->setBillingType($billPayer->isOrganization() ? 'company' : 'person')
            ->setFirstName($billPayer->getFirstName())
            ->setLastName($billPayer->getLastName())
            ->setEmail($billPayer->getEmail())
            ->setPhone($billPayer->getPhone())
            ->setAddress(
                $billPayer->getBillingAddress()->getCity() . ' ' . $billPayer->getBillingAddress()->getAddress()
            )
            ->setConfirmUrl($options['confirm'] ?? '/')
            ->setReturnUrl($options['return'] ?? '/');

        return $result;
    }
}
