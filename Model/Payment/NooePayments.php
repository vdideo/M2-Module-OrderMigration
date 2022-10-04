<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Model\Payment;

class Tun2UPayments extends \Magento\Payment\Model\Method\AbstractMethod
{
	const PAYMENT_METHOD_TUN2U_CODE = 'tun2u_payments';
	/**
	 * Payment method code
	 *
	 * @var string
	 */
	protected $_code = self::PAYMENT_METHOD_TUN2U_CODE;

	/**
	 * Availability option
	 *
	 * @var bool
	 */
	protected $_isOffline = true;
}
