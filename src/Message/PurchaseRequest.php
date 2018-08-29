<?php

namespace Omnipay\PayZone\Message;

use function array_key_exists;
use const FILTER_VALIDATE_BOOLEAN;
use function http_build_query;
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

    protected function boolToString($value)
    {
        $val = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return json_encode($val);
    }

    public function setTransactionTime($time)
    {
        $this->setParameter('transaction_time', $time);

        return $this;
    }

    public function getData()
    {
        $this->validate('amount', 'merchant_id', 'password', 'hash_method', 'pre_shared_key', 'currency', 'transactionReference', 'customer_name', 'address1', 'city', 'notifyUrl');
        $data['amount'] = $this->getAmountInteger();
        $data['currency'] = $this->getCurrencyNumeric();
        $data['orderid'] = $this->getTransactionReference();
        $data['CallbackUrl'] = $this->getNotifyUrl();
        $transactionTime = $this->get('transaction_time');
        if (!$transactionTime) {
            $transactionTime = date('Y-m-d H:i:s P');
        }
        $nextData = [
            'MerchantID' => $this->getMerchantId(),
            'Password' => $this->getPassword(),
            'Amount' => $this->getAmountInteger(),
            'CurrencyCode' => $this->getCurrencyNumeric(),
            'EchoAVSCheckResult' => 'true',
            'EchoCV2CheckResult' => 'true',
            'EchoThreeDSecureAuthenticationCheckResult' => 'true',
            'EchoCardType' => 'true',
            'EchoFraudProtectionCheckResult' => 'true',
            'OrderID' => $this->getTransactionReference(),
            'TransactionType' => 'SALE',
            'TransactionDateTime' => $transactionTime,
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
            'CV2Mandatory' => $this->boolToString($this->get('cv2_mandatory')),
            'Address1Mandatory' => $this->boolToString($this->get('address1_mandatory')),
            'CityMandatory' => $this->boolToString($this->get('city_mandatory')),
            'PostCodeMandatory' => $this->boolToString($this->get('post_code_mandatory')),
            'StateMandatory' => $this->boolToString($this->get('state_mandatory')),
            'CountryMandatory' => $this->boolToString($this->get('country_mandatory')),
            'ResultDeliveryMethod' => 'POST',
        ];

        $hashMethod = $this->getHashMethod();
        $psk = $this->getPreSharedKey();
        $hashArg = $this->generateStringToHash($psk, $hashMethod, $nextData);
        $nextData['HashDigest'] = $this->calculateHashDigest($hashArg, $psk, $hashMethod);
        unset($nextData['Password']);

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

    protected function generateStringToHash($szPreSharedKey, $szHashMethod, $formData)
    {
        $data = array_merge([
            'MerchantID' => '',
            'Password' => '',
            'Amount' => '',
            'CurrencyCode' => '',
            'EchoAVSCheckResult' => '',
            'EchoCV2CheckResult' => '',
            'EchoThreeDSecureAuthenticationCheckResult' => '',
            'EchoFraudProtectionCheckResult' => '',
            'EchoCardType' => '',
            'EchoCardNumberFirstSix' => '',
            'EchoCardNumberLastFour' => '',
            'EchoCardExpiryDate' => '',
            'EchoDonationAmount' => '',
            'AVSOverridePolicy' => '',
            'CV2OverridePolicy' => '',
            'ThreeDSecureOverridePolicy' => '',
            'OrderID' => '',
            'TransactionType' => '',
            'TransactionDateTime' => '',
            'DisplayCancelButton' => '',
            'CallbackURL' => '',
            'OrderDescription' => '',
            'LineItemSalesTaxAmount' => '',
            'LineItemSalesTaxDescription' => '',
            'LineItemQuantity' => '',
            'LineItemAmount' => '',
            'LineItemDescription' => '',
            'CustomerName' => '',
            'DisplayBillingAddress' => '',
            'Address1' => '',
            'Address2' => '',
            'Address3' => '',
            'Address4' => '',
            'City' => '',
            'State' => '',
            'PostCode' => '',
            'CountryCode' => '',
            'EmailAddress' => '',
            'PhoneNumber' => '',
            'DateOfBirth' => '',
            'DisplayShippingDetails' => '',
            'ShippingName' => '',
            'ShippingAddress1' => '',
            'ShippingAddress2' => '',
            'ShippingAddress3' => '',
            'ShippingAddress4' => '',
            'ShippingCity' => '',
            'ShippingState' => '',
            'ShippingPostCode' => '',
            'ShippingCountryCode' => '',
            'ShippingEmailAddress' => '',
            'ShippingPhoneNumber' => '',
            'CustomerNameEditable' => '',
            'EmailAddressEditable' => '',
            'PhoneNumberEditable' => '',
            'DateOfBirthEditable' => '',
            'CV2Mandatory' => '',
            'Address1Mandatory' => '',
            'CityMandatory' => '',
            'PostCodeMandatory' => '',
            'StateMandatory' => '',
            'CountryMandatory' => '',
            'ShippingAddress1Mandatory' => '',
            'ShippingCityMandatory' => '',
            'ShippingPostCodeMandatory' => '',
            'ShippingStateMandatory' => '',
            'ShippingCountryMandatory' => '',
            'ResultDeliveryMethod' => '',
            'ServerResultURL' => '',
            'PaymentFormDisplaysResult' => '',
            'ServerResultURLCookieVariables' => '',
            'ServerResultURLFormVariables' => '',
            'ServerResultURLQueryStringVariables' => '',
            'PrimaryAccountName' => '',
            'PrimaryAccountNumber' => '',
            'PrimaryAccountDateOfBirth' => '',
            'PrimaryAccountPostCode' => '',
            'Skin' => '',
            'PaymentFormContentMode' => '',
            'BreakoutOfIFrameOnCallback' => '',
        ], $formData);
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
        $szReturn = [];
        if ($boIncludePreSharedKeyInString) {
            $szReturn[] = "PreSharedKey={$szPreSharedKey}";
        }

        foreach ($data as $key => $value) {
            if ($value === '' && !array_key_exists($key, $formData)) {
                continue;
            }
            $szReturn[] = "{$key}={$value}";
        }

        return implode("&", $szReturn);
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
