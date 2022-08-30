<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Api;

interface ProductInterface
{

	/**
	 * Places a product.
	 * Returns the placement status as array:
	 * ['success' => false, 'error' => true, 'message' => 'string message'];
	 *
	 * @param array $productData
	 * @return array
	 */
	public function create($productData);

	/**
	 * @inheritdoc
	 */
	public function updateStock();
}