<?php

namespace Omnipay\PayZone;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayZone\Message\CaptureRequest;
use Omnipay\PayZone\Message\CompletePurchaseRequest;
use Omnipay\PayZone\Message\DeleteRequest;
use Omnipay\PayZone\Message\PurchaseRequest;
use Omnipay\PayZone\Message\RefundRequest;

/**
 * PayZone Gateway
 */
class Gateway extends AbstractGateway
{

    public function getName()
    {
        return 'PayZone';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'testMode' => true,
            'pre_shared_key' => '',
            'merchant_id' => '',
            'password' => '',
            'hash_method' => 'SHA1',
        ];
    }

    public function setPreSharedKey($psk)
    {
        $this->setParameter('pre_shared_key', $psk);

        return $this;
    }

    public function setMerchantId($value)
    {
        $this->setParameter('merchant_id', $value);

        return $this;
    }

    public function setHashMethod($value)
    {
        $this->setParameter('hash_method', $value);

        return $this;
    }

    public function setPassword($value)
    {
        $this->setParameter('password', $value);

        return $this;
    }

    public function getPreSharedKey()
    {
        return $this->getParameter('pre_shared_key');
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    public function getHashMethod()
    {
        return $this->getParameter('hash_method');
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param array $parameters
     * @return PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        
        return $this->createRequest('\Omnipay\PayZone\Message\PurchaseRequest', $parameters);
    }

}

