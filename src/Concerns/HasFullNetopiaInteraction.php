<?php

declare(strict_types=1);

/**
 * Contains the HasFullNetopiaInteraction trait.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-26
 *
 */

namespace Vanilo\Netopia\Concerns;

trait HasFullNetopiaInteraction
{
    use HasNetopiaConfig;
    use HasNetopiaCallbackUrls;

    public function __construct(
        string $signature,
        string $publicCertificatePath,
        string $privateCertificatePath,
        bool $isSandbox,
        string $returnUrl,
        string $confirmUrl
    ) {
        $this->signature = $signature;
        $this->publicCertificatePath = $publicCertificatePath;
        $this->privateCertificatePath = $privateCertificatePath;
        $this->isSandbox = $isSandbox;
        $this->returnUrl = $returnUrl;
        $this->confirmUrl = $confirmUrl;
    }
}
