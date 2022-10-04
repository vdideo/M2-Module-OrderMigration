<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

declare(strict_types=1);

namespace Tun2U\OrderMigration\Service;

use Exception;

class ProductService
{
	/**
	 * @var \Tun2U\OrderMigration\Model\Product
	 */
	private $product;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory $productFactory
	 */
	protected $productFactory;

	/**
	 * @var \Tun2U\OrderMigration\Helper\Data $configData
	 */
	protected $configData;

	/**
	 * @var \Tun2U\OrderMigration\Logger\Logger $logger
	 */
	private $logger;

	/**
	 * @var \Tun2U\OrderMigration\Helper\Sync
	 */
	private $syncHelper;

	/**
	 * OrderService constructor.
	 *
	 * @param \Tun2U\OrderMigration\Model\Product $product
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Tun2U\OrderMigration\Helper\Data $configData
	 * @param \Tun2U\OrderMigration\Logger\Logger $logger
	 * @param \Tun2U\OrderMigration\Helper\Sync $syncHelper
	 */
	public function __construct(
		\Tun2U\OrderMigration\Model\Product $product,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Tun2U\OrderMigration\Helper\Data $configData,
		\Tun2U\OrderMigration\Logger\Logger $logger,
		\Tun2U\OrderMigration\Helper\Sync $syncHelper
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
