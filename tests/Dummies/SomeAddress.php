<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Tests\Dummies;

use Vanilo\Contracts\Address;

class SomeAddress implements Address
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountryCode(): string
    {
        return 'DK';
    }

    public function getProvinceCode(): ?string
    {
        return '85';
    }

    public function getPostalCode(): ?string
    {
        return '4874';
    }

    public function getCity(): ?string
    {
        return 'Gedser';
    }

    public function getAddress(): string
    {
        return '23 Strandvej';
    }

    public function getAddress2(): ?string
    {
        return null;
    }
}
