<?php

namespace Vanilo\Netopia\Factories;

use Vanilo\Netopia\Messages\NetopiaPaymentRequest;
use Vanilo\Payment\Contracts\Payment;

class RequestFactory
{
    public static function create(bool $isSandbox, string $signature, string $publicCertificatePath, Payment $payment, array $options = []): NetopiaPaymentRequest
    {
        $result         = new NetopiaPaymentRequest();
        $billingAddress = $payment->getPayable()->getBillPayer();

        $result
            ->setIsSandbox($isSandbox)
            ->setSignature($signature)
            ->setPublicCertificatePath($publicCertificatePath)
            ->setPaymentId($payment->getPaymentId())
            ->setCurrency($payment->getCurrency())
            ->setAmount($payment->getAmount())
            ->setTimestamp(date('YmdHis'))
            ->setDetails($options['description'] ?? __('Order no. :number', ['number' => $payment->getPayable()->getTitle()]))
            ->setBillingType($billingAddress->isOrganization() ? 'company' : 'person')
            ->setFirstName($billingAddress->getFirstName())
            ->setLastName($billingAddress->getLastName())
            ->setEmail($billingAddress->getEmail())
            ->setPhone($billingAddress->getPhone())
            ->setAddress(
                $billingAddress->getBillingAddress()->getCity() . ' ' . $billingAddress->getBillingAddress()->getAddress()
            )
            ->setConfirmUrl($options['confirm'] ?? '/')
            ->setReturnUrl($options['return'] ?? '/');

        return $result;
    }
}
