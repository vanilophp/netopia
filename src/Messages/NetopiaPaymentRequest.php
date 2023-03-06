<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Messages;

use DOMDocument;
use Illuminate\Support\Facades\View;
use Vanilo\Netopia\Concerns\HasFullNetopiaInteraction;
use Vanilo\Netopia\Exceptions\InvalidNetopiaKeyException;
use Vanilo\Payment\Contracts\PaymentRequest;

class NetopiaPaymentRequest implements PaymentRequest
{
    use HasFullNetopiaInteraction;

    private string $paymentId;

    private string $timestamp;

    private string $currency;

    private float $amount;

    private string $details;

    private string $billingType;

    private string $firstName;

    private string $lastName;

    private string $email;

    private ?string $phone;

    private string $address;

    private string $view = 'netopia::_request';

    public function getHtmlSnippet(array $options = []): ?string
    {
        return View::make(
            $this->view,
            array_merge(
                $this->encryptData(),
                [
                    'url' => $this->getUrl(),
                    'autoRedirect' => $options['autoRedirect'] ?? false
                ]
            )
        )->render();
    }

    public function willRedirect(): bool
    {
        return true;
    }

    public function setPaymentId(string $paymentId): self
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function setTimestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function setBillingType(string $billingType): self
    {
        $this->billingType = $billingType;

        return $this;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setConfirmUrl(string $confirmUrl): self
    {
        $this->confirmUrl = $confirmUrl;

        return $this;
    }

    public function setReturnUrl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    private function encryptData(): array
    {
        $publicKey = openssl_pkey_get_public("file://{$this->publicCertificatePath}");

        if (false === $publicKey) {
            throw InvalidNetopiaKeyException::fromPath($this->publicCertificatePath);
        }

        $encData = null;
        $envKeys = [];
        $publicKey = [$publicKey];

        // https://github.com/mobilpay/composer/issues/6
        openssl_seal($this->getXml(), $encData, $envKeys, $publicKey, 'RC4');

        return [
            'data' => base64_encode($encData),
            'envKey' => base64_encode($envKeys[0])
        ];
    }

    private function getXml(): string
    {
        $xml = new DOMDocument('1.0', 'utf-8');

        $orderNode = $xml->createElement('order');
        $orderNode->setAttribute('type', 'card');
        $orderNode->setAttribute('id', $this->paymentId);
        $orderNode->setAttribute('timestamp', $this->timestamp);
        $xml->appendChild($orderNode);

        $orderNode->appendChild($xml->createElement('signature', $this->signature));

        $invoiceNode = $xml->createElement('invoice');
        $invoiceNode->setAttribute('currency', $this->currency);
        $invoiceNode->setAttribute('amount', number_format($this->amount, 2, '.', ''));

        $invoiceNode->appendChild($xml->createElement('details', $this->details));

        $contactNode = $xml->createElement('contact_info');

        $billingNode = $xml->createElement('billing');
        $billingNode->setAttribute('type', $this->billingType);

        $billingNode->appendChild($xml->createElement('first_name', $this->firstName ?? ''));
        $billingNode->appendChild($xml->createElement('last_name', $this->lastName ?? ''));
        $billingNode->appendChild($xml->createElement('email', $this->email ?? ''));
        $billingNode->appendChild($xml->createElement('address', $this->address ?? ''));
        $billingNode->appendChild($xml->createElement('mobile_phone', $this->phone ?? ''));

        $contactNode->appendChild($billingNode);
        $invoiceNode->appendChild($contactNode);
        $orderNode->appendChild($invoiceNode);

        $urlNode = $xml->createElement('url');

        $urlNode->appendChild($xml->createElement('confirm', $this->confirmUrl));
        $urlNode->appendChild($xml->createElement('return', $this->returnUrl));

        $orderNode->appendChild($urlNode);

        return $xml->saveXML();
    }

    private function getUrl(): string
    {
        return $this->isSandbox ? 'https://sandboxsecure.mobilpay.ro' : 'https://secure.mobilpay.ro';
    }
}
