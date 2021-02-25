<?php

declare(strict_types=1);

/**
 * Contains the HasNetopiaConfig trait.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-25
 *
 */

namespace Vanilo\Netopia\Concerns;

trait HasNetopiaConfig
{
    private string $signature;

    private string $publicCertificatePath;

    private string $privateCertificatePath;

    private bool $isSandbox;
}
