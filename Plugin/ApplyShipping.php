<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Plugin;

class ApplyShipping
{
     /**
     * Disables "nooeshipping" shipping method on the frontend side.
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
    )
    {
        if ($carrierCode == 'nooeshipping') {
            return false;
        }

        return $proceed($carrierCode, $request);
    }
}
