<?php

declare(strict_types=1);

/**
 * Contains the PaymentResponseExpiredCardTest class.
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

class PaymentResponseExpiredCardTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_actual_netopia_xml()
    {
        $response = new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/expired_card.xml')
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
        $this->assertEquals('IRy1ciaxpCZgEDQ8VBjQc', $response->getPaymentId());
    }

    /** @test */
    public function it_correctly_returns_the_paid_amount_in_the_original_currency()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(10, $response->getAmountPaid());
    }

    /** @test */
    public function it_returns_the_payment_id_as_the_transaction_id()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('IRy1ciaxpCZgEDQ8VBjQc', $response->getTransactionId());
    }

    /** @test */
    public function it_returns_the_crc()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('10d50a2bac0b1039eafc4018d81a3e67', $response->getCrc());
    }

    /** @test */
    public function it_returns_the_message_from_the_gateway()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals('Card expirat', $response->getMessage());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_original_currency()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(10.00, $response->getSubmittedAmountInOriginalCurrency());
    }

    /** @test */
    public function it_can_obtain_the_submitted_amount_in_converted_currency()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(40.21, $response->getSubmittedAmount());
    }

    /** @test */
    public function it_can_obtain_the_processed_amount()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertEquals(40.21, $response->getProcessedAmount());
    }

    /** @test */
    public function the_action_is_paid()
    {
        $response = $this->loadExpiredCardResponse();
        $this->assertInstanceOf(NetopiaAction::class, $response->action);
        $this->assertTrue($response->action->equals(NetopiaAction::PAID()));
    }

    private function loadExpiredCardResponse(): NetopiaPaymentResponse
    {
        return new NetopiaPaymentResponse(
            simplexml_load_file(__DIR__ . '/data/expired_card.xml')
        );
    }
}
