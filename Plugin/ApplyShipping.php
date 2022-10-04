<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Plugin;

class ApplyShipping
{
	/**
	 * Disables "tun2ushipping" shipping method on the frontend side.
	 *
	 * @param \Magento\Shipping\Model\Shipping $subject
	 * @param \Closure $proceed
	 * @param string $carrierCode
	 * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
	 * @return mixed
	 */
	public function aroundCollectCarrierRates(
		\Magento\Shipping\Model\Shipping $subject,
		\Closure $proceed,
		$carrierCode,
		$request
	) {
		if ($carrierCode == 'tun2ushipping') {
			return false;
		}

		return $proceed($carrierCode, $request);
	}
}
