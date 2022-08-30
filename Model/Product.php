<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Model;

use Exception;
use Nooe\M2Connector\Api\ProductInterface;

class Product implements ProductInterface
{
	/**
	 * API request endpoint
	 */
	const API_REQUEST_ENDPOINT = 'products';

	/**
	 * @var \Nooe\M2Connector\Helper\Data
	 */
	private $helperData;

	/**
	 * @var \Nooe\M2Connector\Model\Connector
	 */
	private $connector;

	/**
	 * @var \Nooe\M2Connector\Logger\Logger
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
	 * @var \Nooe\M2Connector\Helper\Data
	 */
	private $configData;

	/**
	 * Order constructor.
	 *
	 * @param \Nooe\M2Connector\Helper\Data $helperData
	 * @param \Nooe\M2Connector\Model\Connector $connector
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
	 * @param \Magento\Catalog\Model\ProductRepository $productRepository,
	 * @param \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement,
	 * @param \Nooe\M2Connector\Helper\Data $configData
	 * @param \Nooe\M2Connector\Logger\Logger $logger
	 */
	public function __construct(
		\Nooe\M2Connector\Helper\Data $helperData,
		\Nooe\M2Connector\Model\Connector $connector,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement,
		\Nooe\M2Connector\Helper\Data $configData,
		\Nooe\M2Connector\Logger\Logger $logger
	) {
		$this->helperData = $helperData;
		$this->connector = $connector;
		$this->_storeManager = $storeManager;
		$this->stockRegistry = $stockRegistry;
		$this->_productRepository = $productRepository;
		$this->_linkManagement = $linkManagement;
		$this->configData = $configData;
		$this->logger = $logger;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create($productData)
	{
		$store = $this->_storeManager->getStore();
		$websiteId = $this->_storeManager->getStore()->getWebsiteId();
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
				// $product = $this->connector->doRequest(self::API_REQUEST_ENDPOINT . '/' . $sku);
				// var_dump($productStockItem);
				// die();

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

					throw new Exception($e->getMessage());
					$this->logger->error($e->getMessage());
				}
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			$this->logger->error($e->getMessage());
		}
	}

	private function getStockBySku($sku)
	{
		$stockStatus = $this->stockRegistry->getStockStatusBySku($sku, $this->_storeManager->getWebsite()->getId());
		$stockData = $stockStatus->getStockItem()->getData();

		return $stockData;
	}
}