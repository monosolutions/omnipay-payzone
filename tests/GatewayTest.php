<?php

namespace Omnipay\PayZone;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase([
            'pre_shared_key' => 'secret-key',
            'merchant_id' => '1',
            'amount' => '10.00',
            'transactionReference' => 1,
            'currency' => 'EUR',
            'customer_name' => 'John Doe',
        ]);
        $this->assertInstanceOf('Omnipay\PayZone\Message\PurchaseRequest', $request);
        $request->setAddress1('Bld. Dreams')->setCity('Los Angeles');

        $this->assertSame('10.00', $request->getAmount());
        $this->assertArrayHasKey('HashDigest', $request->getData());
    }
}
