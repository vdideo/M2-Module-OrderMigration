<?php
namespace Nooe\M2Connector\Model\Payment;

class NooePayments extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_NOOE_CODE = 'nooe_payments';
    /**
    * Payment method code
    *
    * @var string
    */
    protected $_code = self::PAYMENT_METHOD_NOOE_CODE;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}

