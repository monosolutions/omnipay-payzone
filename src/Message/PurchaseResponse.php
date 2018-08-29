<?php
namespace Omnipay\PayZone\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    const FORM_URL = "https://mms.payzoneonlinepayments.com/Pages/PublicPages/PaymentForm.aspx";
    protected $redirectMethod = 'POST';

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getEndpoint()
    {
        return self::FORM_URL;
    }

    public function getRedirectUrl()
    {
        return $this->getEndpoint();
    }

    public function getRedirectMethod()
    {
        return $this->redirectMethod;
    }

    public function getRedirectData()
    {
        return $this->data;
    }
}

