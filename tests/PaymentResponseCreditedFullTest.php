<?php

declare(strict_types=1);

/**
 * Contains the PaymentResponseCreditedFullTest class.
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

class PaymentResponseCreditedFullTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_actual_netopia_xml()
    {
        $response = new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/credited_full_response.xml')
        );

        $this->assertInstanceOf(NetopiaPaymentResponse::class, $response);
    }

    /** @test */
    public function it_detects_whether_the_operation_succeeded()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertTrue($response->wasSuccessful());
    }

    /** @test */
    public function it_reads_the_payment_id()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals('O4xqNBJlVSSBDi409yA4i', $response->getPaymentId());
    }

    /** @test */
    public function it_correctly_returns_the_paid_amount_in_the_original_currency()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals(-10.00, $response->getAmountPaid());
    }

    /** @test */
    public function it_returns_the_payment_id_as_the_transaction_id()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals('O4xqNBJlVSSBDi409yA4i', $response->getTransactionId());
    }

    /** @test */
    public function it_returns_the_crc()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals('ff68ce796345b504f8e6f6dc0846dfa5', $response->getCrc());
    }

    /** @test */
    public function it_returns_the_message_from_the_gateway()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals('Tranzactia aprobata', $response->getMessage());
    }

    /** @test */
    public function the_action_is_credit()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertInstanceOf(NetopiaAction::class, $response->action);
        $this->assertTrue($response->action->equals(NetopiaAction::CREDIT()));
    }

    /** @test */
    public function the_status_is_refunded()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals(PaymentStatus::REFUNDED, $response->getStatus()->value());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_original_currency()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals(10.00, $response->getSubmittedAmountInOriginalCurrency());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_converted_currency()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals(41.07, $response->getSubmittedAmount());
    }

    /** @test */
    public function it_can_obtain_the_processed_amount()
    {
        $response = $this->loadCreditedFullResponse();
        $this->assertEquals(41.07, $response->getProcessedAmount());
    }

    private function loadCreditedFullResponse(): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/credited_full_response.xml')
        );
    }
}
