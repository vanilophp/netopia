<?php

declare(strict_types=1);

/**
 * Contains the PaymentResponseDeclinedCardTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-05-12
 *
 */

namespace Vanilo\Netopia\Tests;

use Vanilo\Netopia\Messages\NetopiaPaymentResponse;
use Vanilo\Netopia\Models\NetopiaAction;
use Vanilo\Payment\Models\PaymentStatus;

class PaymentResponseDeclinedCardTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_actual_netopia_xml()
    {
        $response = new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/card_declined_response.xml')
        );

        $this->assertInstanceOf(NetopiaPaymentResponse::class, $response);
    }

    /** @test */
    public function it_detects_whether_the_operation_succeeded()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertFalse($response->wasSuccessful());
    }

    /** @test */
    public function it_reads_the_payment_id()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('2oeffmxgU0ht-659C2B_F', $response->getPaymentId());
    }

    /** @test */
    public function it_correctly_returns_the_paid_amount_in_the_original_currency()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(375, $response->getAmountPaid());
    }

    /** @test */
    public function it_returns_the_payment_id_as_the_transaction_id()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('2oeffmxgU0ht-659C2B_F', $response->getTransactionId());
    }

    /** @test */
    public function it_returns_the_crc()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('0d37f453964c29606d631a1efe0283e2', $response->getCrc());
    }

    /** @test */
    public function it_returns_netopia_error_code_label_as_the_message_because_the_supplied_message_is_empty()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('Transaction declined', $response->getMessage());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_original_currency()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(375.00, $response->getSubmittedAmountInOriginalCurrency());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_converted_currency()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(375, $response->getSubmittedAmount());
    }

    /** @test */
    public function it_can_obtain_the_processed_amount()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(375, $response->getProcessedAmount());
    }

    /** @test */
    public function the_action_is_paid()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertInstanceOf(NetopiaAction::class, $response->action);
        $this->assertTrue($response->action->equals(NetopiaAction::PAID()));
    }

    /** @test */
    public function the_status_is_declined()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(PaymentStatus::DECLINED, $response->getStatus()->value());
    }

    private function loadExpiredCardResponse(): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/card_declined_response.xml')
        );
    }
}
