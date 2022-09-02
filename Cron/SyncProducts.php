<?php

/**
 * @category    Nooe
 * @package     Nooe_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\Connector\Cron;


class SyncProducts
{
	/**
	 * @var \Nooe\Connector\Service\ProductService
	 */
	protected $productService;

	/**
	 * ProductService constructor.
	 * 
	 * @param \Nooe\Connector\Service\ProductService $productService
	 */
	public function __construct(
		\Nooe\Connector\Service\ProductService $productService
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
