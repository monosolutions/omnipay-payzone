<?php

namespace Omnipay\PayZone;

use Omnipay\PayZone\Message\PurchaseResponse;
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
        $request->setNotifyUrl('https://httpreq.com/dawn-heart-60q08azn');
        $request->setAddress1('Bld. Dreams')->setCity('Los Angeles');
        $request->setTransactionTime('2018-08-29 15:14:48 +02:00');

        $this->assertSame('10.00', $request->getAmount());
        $this->assertArrayHasKey('HashDigest', $request->getData());
        $this->assertArrayHasKey('CallbackURL', $request->getData());
        $this->assertArrayNotHasKey('Password', $request->getData());
        $response = $request->send();
        $this->assertInstanceOf(PurchaseResponse::class, $response);
        if ($response instanceof PurchaseResponse) {
            $redirectResponse = $response->getRedirectResponse();
            $this->assertContains('<form', $redirectResponse->getContent());
            $this->assertContains('name="HashDigest"', $redirectResponse->getContent());
        }
    }
}
