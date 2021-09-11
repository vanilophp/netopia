<?php

declare(strict_types=1);

/**
 * Contains the PaymentResponseCreditedPartialTest class.
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

class PaymentResponseCreditedPartialTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_actual_netopia_xml()
    {
        $response = new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/credited_partial_response.xml')
        );

        $this->assertInstanceOf(NetopiaPaymentResponse::class, $response);
    }

    /** @test */
    public function it_detects_whether_the_operation_succeeded()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertTrue($response->wasSuccessful());
    }

    /** @test */
    public function it_reads_the_payment_id()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals('lr7XlLuCYzrJ9-2ju8vVN', $response->getPaymentId());
    }

    /** @test */
    public function it_correctly_returns_the_paid_amount_in_the_original_currency()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals(-12.17, $response->getAmountPaid());
    }

    /** @test */
    public function it_returns_the_payment_id_as_the_transaction_id()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals('lr7XlLuCYzrJ9-2ju8vVN', $response->getTransactionId());
    }

    /** @test */
    public function it_returns_the_crc()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals('e6ed9a19539d904dbe6e31346b7ce125', $response->getCrc());
    }

    /** @test */
    public function it_returns_the_message_from_the_gateway()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals('Tranzactia aprobata', $response->getMessage());
    }

    /** @test */
    public function the_action_is_credit()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertInstanceOf(NetopiaAction::class, $response->action);
        $this->assertTrue($response->action->equals(NetopiaAction::CREDIT()));
    }

    /** @test */
    public function the_status_is_partially_refunded()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals(PaymentStatus::PARTIALLY_REFUNDED, $response->getStatus()->value());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_original_currency()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals(17.69, $response->getSubmittedAmountInOriginalCurrency());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_converted_currency()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals(72.65, $response->getSubmittedAmount());
    }

    /** @test */
    public function it_can_obtain_the_processed_amount()
    {
        $response = $this->loadCreditedPartialResponse();
        $this->assertEquals(50.00, $response->getProcessedAmount());
    }

    private function loadCreditedPartialResponse(): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/credited_partial_response.xml')
        );
    }
}
