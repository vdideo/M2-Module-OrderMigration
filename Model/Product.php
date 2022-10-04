<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Model;

use Exception;
use Tun2U\OrderMigration\Api\ProductInterface;

class Product implements ProductInterface
{
	/**
	 * API request endpoint
	 */
	const API_REQUEST_ENDPOINT = 'products';

	/**
	 * @var \Tun2U\OrderMigration\Helper\Data
	 */
	private $helperData;

	/**
	 * @var \Tun2U\OrderMigration\Model\OrderMigration
	 */
	private $connector;

	/**
	 * @var \Tun2U\OrderMigration\Logger\Logger
	 */
	private $logger;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\CatalogInventory\Model\StockRegistry;
	 */
	private $stockRegistry;

	/**
	 * @var \Magento\Catalog\Model\ProductRepository
	 */
	protected $_productRepository;

	/**
	 * @var \Magento\ConfigurableProduct\Api\LinkManagementInterface
	 */
	protected $_linkManagement;

	/**
	 * @var \Tun2U\OrderMigration\Helper\Data
	 */
	private $configData;


	/**
	 * @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
	 */
	private $getSalableQuantityDataBySku;

	/**
	 * Order constructor.
	 *
	 * @param \Tun2U\OrderMigration\Helper\Data $helperData
	 * @param \Tun2U\OrderMigration\Model\OrderMigration $connector
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
	 * @param \Magento\Catalog\Model\ProductRepository $productRepository
	 * @param \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement
	 * @param \Tun2U\OrderMigration\Helper\Data $configData
	 * @param \Tun2U\OrderMigration\Logger\Logger $logger
	 * @param \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $getSalableQuantityDataBySku
	 */
	public function __construct(
		\Tun2U\OrderMigration\Helper\Data $helperData,
		\Tun2U\OrderMigration\Model\OrderMigration $connector,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement,
		\Tun2U\OrderMigration\Helper\Data $configData,
		\Tun2U\OrderMigration\Logger\Logger $logger,
		\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $getSalableQuantityDataBySku
	) {
		$this->helperData = $helperData;
		$this->connector = $connector;
		$this->_storeManager = $storeManager;
		$this->stockRegistry = $stockRegistry;
		$this->_productRepository = $productRepository;
		$this->_linkManagement = $linkManagement;
		$this->configData = $configData;
		$this->logger = $logger;
		$this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create($productData)
	{
		// $store = $this->_storeManager->getStore();
		// $websiteId = $this->_storeManager->getStore()->getWebsiteId();
	}

	/**
	 * {@inheritdoc}
	 */
	public function updateStock()
	{
		$productSku = $this->helperData->getProductSKu();

		try {
			$stockData = $this->getStockBySku($productSku);
			$stockUpdateSkus = [];

			if ($stockData['type_id'] == 'configurable') {
				$childProducts = $this->_linkManagement->getChildren($productSku);
				echo $productSku . " (configurable)\n";
				if (count($childProducts)) {
					foreach ($childProducts as $childProduct) {
						$stockData = $this->getStockBySku($childProduct->getSku());
						$stockUpdateSkus[$childProduct->getSku()] = $stockData['qty'];
					}
				}
			} else {
				$stockUpdateSkus[$productSku] = $stockData['qty'];
				echo $productSku . ": " . $stockData['qty'] . "\n";
			}

			foreach ($stockUpdateSkus as $sku => $qty) {
				echo ' |- ' . $sku . " (simple): " . $qty;

				try {
					$productStockItem = $this->connector->doRequest('stockItems/' . $sku);

					if (isset($productStockItem->item_id)) {
						$itemId = $productStockItem->item_id;
						$isInStock = $productStockItem->is_in_stock;
						$data = ['stockItem' => ['qty' => $qty, 'is_in_stock' => $isInStock]];
						$stockData = $this->getStockBySku($productSku);
						$response = $this->connector->doRequest(self::API_REQUEST_ENDPOINT . '/' . $sku . '/stockItems/' . $itemId, 'PUT', $data);
						echo ' -> Stock updated';
					} else {
						echo ' -> SKU doesn\'t exist';
					}

					echo "\n";
				} catch (Exception $e) {
					$this->logger->error($e->getMessage());
					throw new Exception($e->getMessage());
				}
			}
		} catch (Exception $e) {
			$this->logger->error($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

	private function getStockBySku($sku)
	{
		$storeId = $this->_storeManager->getStore()->getId();
		$stockStatus = $this->stockRegistry->getStockStatusBySku($sku, $this->_storeManager->getWebsite()->getId());
		$stockData = $stockStatus->getStockItem()->getData();

		if ($this->configData->getSalableQuantity($storeId)) {
			$salableQty = $this->getSalableQuantityDataBySku->execute($sku);

			if (count($salableQty)) {
				$salableQty[0]['type_id'] = $stockData['type_id'];
				$stockData = $salableQty[0];
			}
		}

		return $stockData;
	}
}