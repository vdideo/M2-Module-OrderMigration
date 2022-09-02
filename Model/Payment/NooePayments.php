<?php

/**
 * @category    Nooe
 * @package     Nooe_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\Connector\Model\Payment;

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
