<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Factories;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Vanilo\Netopia\Concerns\HasFullNetopiaInteraction;
use Vanilo\Netopia\Messages\NetopiaPaymentRequest;
use Vanilo\Payment\Contracts\Payment;

final class RequestFactory
{
    use HasFullNetopiaInteraction;

    public function create(Payment $payment, array $options = []): NetopiaPaymentRequest
    {
        $result = new NetopiaPaymentRequest(
            $this->signature,
            $this->publicCertificatePath,
            $this->privateCertificatePath,
            $this->isSandbox,
            $this->absUrl($this->returnUrl),
            $this->absUrl($this->confirmUrl)
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
            );

        if (isset($options['confirm_url'])) {
            $result->setConfirmUrl($this->absUrl($options['confirm_url']));
        }

        if (isset($options['return_url'])) {
            $result->setReturnUrl($this->absUrl($options['return_url']));
        }

        if (isset($options['view'])) {
            $result->setView($options['view']);
        }

        return $result;
    }

    private function absUrl(string $uri): string
    {
        if (!Str::startsWith($uri, 'http')) {
            $uri = URL::to($uri);
        }

        return $uri;
    }
}
