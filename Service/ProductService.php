<?php

/**
 * @category    Nooe
 * @package     Nooe_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

declare(strict_types=1);

namespace Nooe\Connector\Service;

use Exception;

class ProductService
{
	/**
	 * @var \Nooe\Connector\Model\Product
	 */
	private $product;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory $productFactory
	 */
	protected $productFactory;

	/**
	 * @var \Nooe\Connector\Helper\Data $configData
	 */
	protected $configData;

	/**
	 * @var \Nooe\Connector\Logger\Logger $logger
	 */
	private $logger;

	/**
	 * @var \Nooe\Connector\Helper\Sync
	 */
	private $syncHelper;

	/**
	 * OrderService constructor.
	 *
	 * @param \Nooe\Connector\Model\Product $product
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Nooe\Connector\Helper\Data $configData
	 * @param \Nooe\Connector\Logger\Logger $logger
	 * @param \Nooe\Connector\Helper\Sync $syncHelper
	 */
	public function __construct(
		\Nooe\Connector\Model\Product $product,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Nooe\Connector\Helper\Data $configData,
		\Nooe\Connector\Logger\Logger $logger,
		\Nooe\Connector\Helper\Sync $syncHelper
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
