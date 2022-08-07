<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Api;

interface OrderInterface
{
    /**
     * Places an order.
     * Returns the placement status as array:
     * ['success' => false, 'error' => true, 'message' => 'string message'];
     *
     * @param array $orderData
     * @return array
     */
	public function create($orderData);

    /**
     * Gets lists orders from remote Magento store that match search criteria which is specified in this method.
     * If an $incrementid was passed as an argument, it will return the order corresponding to that increment id.
     *
     * @param string|null $incrementId
     * @return array
     * @throws \Exception
     */
	public function getList($incrementId = null);
}