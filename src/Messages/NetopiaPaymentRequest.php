<?php

namespace Vanilo\Netopia\Messages;

use DOMDocument;
use Illuminate\Support\Facades\View;
use Vanilo\Payment\Contracts\PaymentRequest;

class NetopiaPaymentRequest implements PaymentRequest
{
    private bool $isSandbox;

    private string $paymentId;

    private string $timestamp;

    private string $signature;

    private string $publicCertificatePath;

    private string $currency;

    private float $amount;

    private string $details;

    private string $billingType;

    private string $firstName;

    private string $lastName;

    private string $email;

    private string $phone;

    private string $address;

    private string $confirmUrl;

    private string $returnUrl;

    public function getHtmlSnippet(array $options = []): ?string
    {
        return View::make('netopia::_request',
            array_merge(
                $this->encryptData(),
                [
                    'url'          => $this->getUrl(),
                    'autoRedirect' => $options['autoRedirect'] ?? false
                ]
            )
        )->render();
    }

    private function encryptData(): array
    {
        $publicKey = openssl_pkey_get_public("file://{$this->publicCertificatePath}");

        if (!$publicKey) {
            throw new \Error("The public following public key path '{$this->publicCertificatePath}' is invalid");
        }

        $encData   = null;
        $envKeys   = null;
        $publicKey = array($publicKey);

        openssl_seal($this->getXml(), $encData, $envKeys, $publicKey, 'RC4');

        return [
            'data'   => base64_encode($encData),
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
        $invoiceNode->setAttribute('amount', $this->amount);

        $invoiceNode->appendChild($xml->createElement('details', $this->details));

        $contactNode = $xml->createElement('contact_info');

        $billingNode = $xml->createElement('billing');
        $billingNode->setAttribute('type', $this->billingType);

        $billingNode->appendChild($xml->createElement('first_name', $this->firstName));
        $billingNode->appendChild($xml->createElement('last_name', $this->lastName));
        $billingNode->appendChild($xml->createElement('email', $this->email));
        $billingNode->appendChild($xml->createElement('address', $this->address));
        $billingNode->appendChild($xml->createElement('mobile_phone', $this->phone));

        $contactNode->appendChild($billingNode);
        $invoiceNode->appendChild($contactNode);
        $orderNode->appendChild($invoiceNode);

        $urlNode = $xml->createElement('url');

        $urlNode->appendChild($xml->createElement('confirm', $this->confirmUrl));
        $urlNode->appendChild($xml->createElement('return', $this->returnUrl));

        $orderNode->appendChild($urlNode);

        return $xml->saveHTML();
    }

    public function getUrl()
    {
        return $this->isSandbox ? 'http://sandboxsecure.mobilpay.ro' : 'https://secure.mobilpay.ro';
    }

    public function willRedirect(): bool
    {
        return true;
    }

    public function setPaymentId(string $paymentId): NetopiaPaymentRequest
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function setTimestamp(string $timestamp): NetopiaPaymentRequest
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function setSignature(string $signature): NetopiaPaymentRequest
    {
        $this->signature = $signature;

        return $this;
    }

    public function setCurrency(string $currency): NetopiaPaymentRequest
    {
        $this->currency = $currency;

        return $this;
    }

    public function setAmount(float $amount): NetopiaPaymentRequest
    {
        $this->amount = $amount;

        return $this;
    }

    public function setDetails(string $details): NetopiaPaymentRequest
    {
        $this->details = $details;

        return $this;
    }

    public function setBillingType(string $billingType): NetopiaPaymentRequest
    {
        $this->billingType = $billingType;

        return $this;
    }

    public function setFirstName(string $firstName): NetopiaPaymentRequest
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(string $lastName): NetopiaPaymentRequest
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setEmail(string $email): NetopiaPaymentRequest
    {
        $this->email = $email;

        return $this;
    }

    public function setPhone(string $phone): NetopiaPaymentRequest
    {
        $this->phone = $phone;

        return $this;
    }

    public function setAddress(string $address): NetopiaPaymentRequest
    {
        $this->address = $address;

        return $this;
    }

    public function setConfirmUrl(string $confirmUrl): NetopiaPaymentRequest
    {
        $this->confirmUrl = $confirmUrl;

        return $this;
    }

    public function setReturnUrl(string $returnUrl): NetopiaPaymentRequest
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    public function setPublicCertificatePath(string $publicCertificatePath): NetopiaPaymentRequest
    {
        $this->publicCertificatePath = $publicCertificatePath;

        return $this;
    }

    public function setIsSandbox(string $isSandbox): NetopiaPaymentRequest
    {
        $this->isSandbox = $isSandbox;

        return $this;
    }
}
