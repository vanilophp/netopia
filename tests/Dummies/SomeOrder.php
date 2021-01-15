<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Tests\Dummies;

use Vanilo\Contracts\Billpayer;
use Vanilo\Contracts\Payable;

class SomeOrder implements Payable
{
    private $amount;

    private $currency;

    private $billPayer;

    public function __construct(float $amount = 99, string $currency = 'EUR')
    {
        $this->amount    = $amount;
        $this->currency  = $currency;
        $this->billPayer = new SomeBillPayer();
    }

    public function getPayableId(): string
    {
        return uniqid();
    }

    public function getPayableType(): string
    {
        return 'order';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getBillpayer(): ?Billpayer
    {
        return $this->billPayer;
    }
}
