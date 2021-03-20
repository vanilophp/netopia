<?php

declare(strict_types=1);

/**
 * Contains the PaymentResponseSuccessTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-02-27
 *
 */

namespace Vanilo\Netopia\Tests;

use Vanilo\Netopia\Messages\NetopiaPaymentResponse;
use Vanilo\Netopia\Models\NetopiaAction;
use Vanilo\Payment\Models\PaymentStatus;

class PaymentResponseSuccessTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_actual_netopia_xml()
    {
        $response = new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/success_response.xml')
        );

        $this->assertInstanceOf(NetopiaPaymentResponse::class, $response);
    }

    /** @test */
    public function it_detects_whether_the_operation_succeeded()
    {
        $response = $this->loadSuccessResponse();
        $this->assertTrue($response->wasSuccessful());
    }

    /** @test */
    public function it_reads_the_payment_id()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals('4krlFIstz7_Xntw2CWbIH', $response->getPaymentId());
    }

    /** @test */
    public function it_correctly_returns_the_paid_amount_in_the_original_currency()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals(33.00, $response->getAmountPaid());
    }

    /** @test */
    public function it_returns_the_payment_id_as_the_transaction_id()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals('4krlFIstz7_Xntw2CWbIH', $response->getTransactionId());
    }

    /** @test */
    public function it_returns_the_crc()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals('d4d2527836cf7727bbd664b94444c056', $response->getCrc());
    }

    /** @test */
    public function it_returns_the_message_from_the_gateway()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals('Tranzactia aprobata', $response->getMessage());
    }

    /** @test */
    public function the_action_is_confirmed()
    {
        $response = $this->loadSuccessResponse();
        $this->assertInstanceOf(NetopiaAction::class, $response->action);
        $this->assertTrue($response->action->equals(NetopiaAction::CONFIRMED()));
    }

    /** @test */
    public function the_status_is_authorized()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals(PaymentStatus::AUTHORIZED, $response->getStatus()->value());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_original_currency()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals(33.00, $response->getSubmittedAmountInOriginalCurrency());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_converted_currency()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals(132.69, $response->getSubmittedAmount());
    }

    /** @test */
    public function it_can_obtain_the_processed_amount()
    {
        $response = $this->loadSuccessResponse();
        $this->assertEquals(132.69, $response->getProcessedAmount());
    }

    private function loadSuccessResponse(): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/success_response.xml')
        );
    }
}
