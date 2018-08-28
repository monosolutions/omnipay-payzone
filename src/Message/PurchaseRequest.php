<?php

namespace Omnipay\PayZone\Message;

use InvalidArgumentException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Epay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
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

    public function setCustomerName($customerName)
    {
        $this->setParameter('customer_name', $customerName);

        return $this;
    }

    public function setAddress1($address1)
    {
        $this->setParameter('address1', $address1);

        return $this;
    }

    public function setCity($city)
    {
        $this->setParameter('city', $city);

        return $this;
    }

    public function initialize(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value);
        }

        return parent::initialize($parameters);
    }

    public function getData()
    {
        $this->validate('amount', 'merchant_id', 'password', 'hash_method', 'pre_shared_key', 'currency', 'transactionReference', 'customer_name', 'address1', 'city');
        $data['amount'] = $this->getAmountInteger();
        $data['currency'] = $this->getCurrencyNumeric();
        $data['orderid'] = $this->getTransactionReference();
        $data['CallbackUrl'] = $this->getNotifyUrl();

        $nextData = [
            'MerchantID' => $this->getMerchantId(),
            'Password' => $this->getPassword(),
            'Amount' => $this->getAmountInteger(),
            'CurrencyCode' => $this->getCurrencyNumeric(),
            'EchoAVSCheckResult' => 'true',
            'EchoCV2CheckResult' => 'true',
            'EchoThreeDSecureAuthenticationCheckResult' => 'true',
            'EchoCardType' => 'true',
            'OrderID' => $this->getTransactionReference(),
            'TransactionType' => 'SALE',
            'TransactionDateTime' => date('Y-m-d H:i:s P'),
            'CallbackURL' => $this->getNotifyUrl(),
            'OrderDescription' => $this->getDescription(),
            'CustomerName' => $this->get('customer_name'),
            'Address1' => $this->get('address1'),
            'Address2' => $this->get('address2'),
            'Address3' => $this->get('address3'),
            'Address4' => $this->get('address4'),
            'City' => $this->get('city'),
            'State' => $this->get('state'),
            'PostCode' => $this->get('post_code'),
            'CountryCode' => $this->get('country_code'),
            'CV2Mandatory' => $this->get('cv2_mandatory'),
            'Address1Mandatory' => $this->get('address1_mandatory'),
            'CityMandatory' => $this->get('city_mandatory'),
            'PostCodeMandatory' => $this->get('post_code_mandatory'),
            'StateMandatory' => $this->get('state_mandatory'),
            'CountryMandatory' => $this->get('country_mandatory'),
            'ResultDeliveryMethod' => 'POST',
            'ServerResultURL' => '',
            'PaymentFormDisplaysResult' => '',
        ];

        $hashMethod = $this->getHashMethod();
        $psk = $this->getPreSharedKey();
        $hashArg = $this->generateStringToHash($psk, $hashMethod, $nextData);
        $nextData['HashDigest'] = $this->calculateHashDigest($hashArg, $psk, $hashMethod);

        return $nextData;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send()
    {
        return $this->sendData($this->getData());
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

    protected function get($key)
    {
        $getName = 'get' . ucfirst($key);
        if (!method_exists($this, $getName)) {
            return $this->getParameter($key);
        }

        return $this->{$getName}();
    }

    protected function generateStringToHash($szPreSharedKey, $szHashMethod, $data)
    {
        $szReturnString = "";
        $defaults = array_merge([
            'MerchantID' => '',
            'Password' => '',
            'Amount' => '',
            'CurrencyCode' => '',
            'EchoAVSCheckResult' => '',
            'EchoCV2CheckResult' => '',
            'EchoThreeDSecureAuthenticationCheckResult' => '',
            'EchoCardType' => '',
            'OrderID' => '',
            'TransactionType' => '',
            'TransactionDateTime' => '',
            'CallbackURL' => '',
            'OrderDescription' => '',
            'CustomerName' => '',
            'Address1' => '',
            'Address2' => '',
            'Address3' => '',
            'Address4' => '',
            'City' => '',
            'State' => '',
            'PostCode' => '',
            'CountryCode' => '',
            'CV2Mandatory' => '',
            'Address1Mandatory' => '',
            'CityMandatory' => '',
            'PostCodeMandatory' => '',
            'StateMandatory' => '',
            'CountryMandatory' => '',
            'ResultDeliveryMethod' => '',
            'ServerResultURL' => '',
            'PaymentFormDisplaysResult' => '',
        ], $data);
        switch ($szHashMethod) {
            case "MD5":
                $boIncludePreSharedKeyInString = true;
                break;
            case "SHA1":
                $boIncludePreSharedKeyInString = true;
                break;
            case "HMACMD5":
                $boIncludePreSharedKeyInString = false;
                break;
            case "HMACSHA1":
                $boIncludePreSharedKeyInString = false;
                break;
        }
        if ($boIncludePreSharedKeyInString) {
            $szReturnString = "PreSharedKey=" . $szPreSharedKey . "&";
        }

        $szReturnString .=
            "MerchantID=" . $data['MerchantID'] .
            "&Password=" . $data['Password'] .
            "&Amount=" . $data['Amount'] .
            "&CurrencyCode=" . $data['CurrencyCode'] .
            "&EchoAVSCheckResult=" . $data['EchoAVSCheckResult'] .
            "&EchoCV2CheckResult=" . $data['EchoCV2CheckResult'] .
            "&EchoThreeDSecureAuthenticationCheckResult=" . $data['EchoThreeDSecureAuthenticationCheckResult'] .
            "&EchoCardType=" . $data['EchoCardType'] .
            "&OrderID=" . $data['OrderID'] .
            "&TransactionType=" . $data['TransactionType'] .
            "&TransactionDateTime=" . $data['TransactionDateTime'] .
            "&CallbackURL=" . $data['CallbackURL'] .
            "&OrderDescription=" . $data['OrderDescription'] .
            "&CustomerName=" . $data['CustomerName'] .
            "&Address1=" . $data['Address1'] .
            "&Address2=" . $data['Address2'] .
            "&Address3=" . $data['Address3'] .
            "&Address4=" . $data['Address4'] .
            "&City=" . $data['City'] .
            "&State=" . $data['State'] .
            "&PostCode=" . $data['PostCode'] .
            "&CountryCode=" . $data['CountryCode'] .
            "&CV2Mandatory=" . $data['CV2Mandatory'] .
            "&Address1Mandatory=" . $data['Address1Mandatory'] .
            "&CityMandatory=" . $data['CityMandatory'] .
            "&PostCodeMandatory=" . $data['PostCodeMandatory'] .
            "&StateMandatory=" . $data['StateMandatory'] .
            "&CountryMandatory=" . $data['CountryMandatory'] .
            "&ResultDeliveryMethod=" . $data['ResultDeliveryMethod'] .
            "&ServerResultURL=" . $data['ServerResultURL'] .
            "&PaymentFormDisplaysResult=" . $data['PaymentFormDisplaysResult'] .
            '&ServerResultURLCookieVariables=' . '&ServerResultURLFormVariables=' . '&ServerResultURLQueryStringVariables=';

        return $szReturnString;
    }

    protected function calculateHashDigest($input, $psk, $hashMethod)
    {
        switch ($hashMethod) {
            case "MD5":
                $hashDigest = md5($input);
                break;
            case "SHA1":
                $hashDigest = sha1($input);
                break;
            case "HMACMD5":
                $hashDigest = hash_hmac("md5", $input, $psk);
                break;
            case "HMACSHA1":
                $hashDigest = hash_hmac("sha1", $input, $psk);
                break;
            default:
                throw new InvalidArgumentException("Invalid hash type provided: '{$hashMethod}'");
        }

        return ($hashDigest);
    }
}
