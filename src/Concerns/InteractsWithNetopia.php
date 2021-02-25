<?php

declare(strict_types=1);

namespace Vanilo\Netopia\Concerns;

trait InteractsWithNetopia
{
    private string $signature;

    private string $publicCertificatePath;

    private string $privateCertificatePath;

    private bool $isSandbox;

    public function __construct(string $signature, string $publicCertificatePath, string $privateCertificatePath, bool $isSandbox)
    {
        $this->signature = $signature;
        $this->publicCertificatePath = $publicCertificatePath;
        $this->privateCertificatePath = $privateCertificatePath;
        $this->isSandbox = $isSandbox;
    }
}
