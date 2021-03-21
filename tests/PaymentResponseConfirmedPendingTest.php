<?php

declare(strict_types=1);

/**
 * Contains the PaymentResponseConfirmedPendingTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-03-21
 *
 */

namespace Vanilo\Netopia\Tests;

use Vanilo\Netopia\Messages\NetopiaPaymentResponse;
use Vanilo\Netopia\Models\NetopiaAction;
use Vanilo\Payment\Models\PaymentStatus;

class PaymentResponseConfirmedPendingTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_actual_netopia_xml()
    {
        $response = new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/confirmed_pending_response.xml')
        );

        $this->assertInstanceOf(NetopiaPaymentResponse::class, $response);
    }

    /** @test */
    public function it_detects_whether_the_operation_succeeded()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertTrue($response->wasSuccessful());
    }

    /** @test */
    public function it_reads_the_payment_id()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals('O4xqNBJlVSSBDi409yA4i', $response->getPaymentId());
    }

    /** @test */
    public function it_correctly_returns_the_paid_amount_in_the_original_currency()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals(10.00, $response->getAmountPaid());
    }

    /** @test */
    public function it_returns_the_payment_id_as_the_transaction_id()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals('O4xqNBJlVSSBDi409yA4i', $response->getTransactionId());
    }

    /** @test */
    public function it_returns_the_crc()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals('be815e2b5c916fe1cdc23b119639063f', $response->getCrc());
    }

    /** @test */
    public function it_returns_the_message_from_the_gateway()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals('In verificare', $response->getMessage());
    }

    /** @test */
    public function the_action_is_confirmed_pending()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertInstanceOf(NetopiaAction::class, $response->action);
        $this->assertTrue($response->action->equals(NetopiaAction::CONFIRMED_PENDING()));
    }

    /** @test */
    public function the_status_is_on_hold()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals(PaymentStatus::ON_HOLD, $response->getStatus()->value());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_original_currency()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals(10.00, $response->getSubmittedAmountInOriginalCurrency());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_converted_currency()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals(41.07, $response->getSubmittedAmount());
    }

    /** @test */
    public function it_can_obtain_the_processed_amount()
    {
        $response = $this->loadConfirmedPendingResponse();
        $this->assertEquals(41.07, $response->getProcessedAmount());
    }

    private function loadConfirmedPendingResponse(): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/confirmed_pending_response.xml')
        );
    }
}
