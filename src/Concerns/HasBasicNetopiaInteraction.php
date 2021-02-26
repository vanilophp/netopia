<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Concerns;

trait HasBasicNetopiaInteraction
{
    use HasNetopiaConfig;

    public function __construct(
        string $signature,
        string $publicCertificatePath,
        string $privateCertificatePath,
        bool $isSandbox = false
    ) {
        $this->signature = $signature;
        $this->publicCertificatePath = $publicCertificatePath;
        $this->privateCertificatePath = $privateCertificatePath;
        $this->isSandbox = $isSandbox;
    }
}
