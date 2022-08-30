<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Cron;


class SyncProducts
{
	/**
	 * @var \Nooe\M2Connector\Service\ProductService
	 */
	protected $productService;

	/**
	 * ProductService constructor.
	 * 
	 * @param \Nooe\M2Connector\Service\ProductService $productService
	 */
	public function __construct(
		\Nooe\M2Connector\Service\ProductService $productService
	) {

		$this->productService = $productService;
	}

	/**
	 * Synchronize product with other magento store.
	 *
	 * @return void
	 */
	public function execute()
	{
		$this->productService->sync();
	}
}