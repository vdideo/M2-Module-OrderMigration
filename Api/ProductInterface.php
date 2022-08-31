<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
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
