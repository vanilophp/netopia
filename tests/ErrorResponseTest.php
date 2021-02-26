<?php

declare(strict_types=1);

/**
 * Contains the ErrorResponseTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-25
 *
 */

namespace Vanilo\Netopia\Tests;

use Vanilo\Netopia\Exceptions\InvalidNetopiaKeyException;
use Vanilo\Netopia\Exceptions\MalformedNetopiaResponse;

class ErrorResponseTest extends TestCase
{
    /** @test */
    public function it_returns_a_netopia_compliant_xml_http_response_when_response_validation_error_occurs()
    {
        $this->get('/throw-validation-error')
             ->assertStatus(400)
             ->assertHeader('Content-type', 'application/xml; charset="utf-8"')
             ->assertSee('<?xml version="1.0" encoding="utf-8" ?>', false)
             ->assertSee('<crc error_type="1" error_code="400">', false)
             ->assertSee('</crc>', false);
    }

    /** @test */
    public function it_returns_a_netopia_compliant_xml_http_response_when_invalid_key_error_occurs()
    {
        $this->get('/throw-netopia-key-error')
             ->assertStatus(500)
             ->assertHeader('Content-type', 'application/xml; charset="utf-8"')
             ->assertSee('<?xml version="1.0" encoding="utf-8" ?>', false)
             ->assertSee('<crc error_type="2" error_code="500">', false)
             ->assertSee('</crc>', false);
    }

    protected function defineRoutes($router)
    {
        $router->get('/throw-validation-error', function () {
            throw MalformedNetopiaResponse::create();
        });

        $router->get('/throw-netopia-key-error', function () {
            throw InvalidNetopiaKeyException::fromPath('/some/path/server.key');
        });
    }
}
