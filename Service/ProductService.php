<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

declare(strict_types=1);

namespace Nooe\M2Connector\Service;

use Exception;

class ProductService
{
	/**
	 * @var \Nooe\M2Connector\Model\Product
	 */
	private $product;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory $productFactory
	 */
	protected $productFactory;

	/**
	 * @var \Nooe\M2Connector\Helper\Data $configData
	 */
	protected $configData;

	/**
	 * @var \Nooe\M2Connector\Logger\Logger $logger
	 */
	private $logger;

	/**
	 * @var \Nooe\M2Connector\Helper\Sync
	 */
	private $syncHelper;

	/**
	 * OrderService constructor.
	 *
	 * @param \Nooe\M2Connector\Model\Product $product
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Nooe\M2Connector\Helper\Data $configData
	 * @param \Nooe\M2Connector\Logger\Logger $logger
	 * @param \Nooe\M2Connector\Helper\Sync $syncHelper
	 */
	public function __construct(
		\Nooe\M2Connector\Model\Product $product,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Nooe\M2Connector\Helper\Data $configData,
		\Nooe\M2Connector\Logger\Logger $logger,
		\Nooe\M2Connector\Helper\Sync $syncHelper
	) {
		$this->product = $product;
		$this->productFactory = $productFactory;
		$this->configData = $configData;
		$this->logger = $logger;
		$this->syncHelper = $syncHelper;
	}

	/**
	 * Synchronizes the list of orders from a remote Magento store.
	 * If $incrementid was passed as an argument, it will only sync the order corresponding to that increment id.
	 *
	 * @param string|null $incrementId
	 * @return void
	 * @throws \Exception
	 */
	public function sync()
	{
		try {
			$result = $this->product->updateStock();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			$this->logger->error("PRODUCT: " . $e->getMessage());
		}
	}
}
