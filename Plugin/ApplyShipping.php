<?php

namespace Nooe\M2Connector\Plugin;

class ApplyShipping
{
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    )
    {
        /*if ($carrierCode == 'nooe_shipping') {
            return false;
        }*/

        return $proceed($carrierCode, $request);
    }
}
