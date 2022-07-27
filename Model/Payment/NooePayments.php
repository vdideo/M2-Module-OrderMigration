<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

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