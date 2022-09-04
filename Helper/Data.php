<?php

/**
 * @category    Nooe
 * @package     Nooe_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\Connector\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * XML path for Access Token.
	 */
	const ACCESS_TOKEN = 'nooe_connector/settings/access_token';
	/**
	 * XML path for Store Code.
	 */
	const STORE_CODE = 'nooe_connector/settings/store_code';
	/**
	 * XML path for Start Date. Defines the start date for synchronization.
	 */
	const START_DATE = 'nooe_connector/settings/start_date';
	/**
	 * XML path for order Increment ID. Contains the increment id of the last synchronized order.
	 */
	const INCREMENT_ID = 'nooe_connector/settings/increment_id';
	/**
	 * XML path for Order ID. Contains the ID of the last synchronized order.
	 */
	const ORDER_ID = 'nooe_connector/settings/order_id';
	/**
	 * XML path for Order Prefix.
	 * Defines a prefix for 'increment_id' with which orders will be saved during synchronization.
	 */
	const ORDER_PREFIX = 'nooe_connector/settings/order_prefix';
	/**
	 * XML path for Product SKU.
	 * Defines a product SKU for stock synchronization.
	 */
	const PRODUCT_SKU = 'nooe_connector/settings/product_sku';

	/**
	 * @var \Magento\Framework\App\Config\Storage\WriterInterface
	 */
	private $_configWriter;

	/**
	 * Data constructor.
	 *
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter
	) {
		$this->_storeManager = $storeManager;
		$this->_configWriter = $configWriter;
		parent::__construct($context);
	}

	/**
	 * Retrieve module config value.
	 * @param string $config_path
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $store
	 * @return mixed
	 */
	private function getConfig($config_path, $store = null)
	{
		$store = $this->_storeManager->getStore($store);
		$result = $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
		return $result;
	}

	/**
	 * Save module config value to storage.
	 *
	 * @param string $config_path
	 * @param string $value
	 * @param int $storeId
	 * @return void
	 */
	private function writeConfig($config_path, $value, $storeId = 0)
	{
		$this->_configWriter->save($config_path, $value, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Returns access token for authorization bearer.
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getAccessToken($storeId = null)
	{
		return $this->getConfig(self::ACCESS_TOKEN, $storeId);
	}

	/**
	 * Returns store code.
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getStoreCode($storeId = null)
	{
		return $this->getConfig(self::STORE_CODE, $storeId);
	}

	/**
	 * Returns start date for synchronization.
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getStartDate($storeId = null)
	{
		return $this->getConfig(self::START_DATE, $storeId);
	}

	/**
	 * Returns last synchronized order increment ID.
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getIncrementId($storeId = null)
	{
		return $this->getConfig(self::INCREMENT_ID, $storeId);
	}

	/**
	 * Returns last synchronized order ID .
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getOrderId($storeId = null)
	{
		return $this->getConfig(self::ORDER_ID, $storeId);
	}

	/**
	 * Returns order prefix value.
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getOrderPrefix($storeId = null)
	{
		return $this->getConfig(self::ORDER_PREFIX, $storeId);
	}

	/**
	 * Returns product sku value.
	 *
	 * @param null|string|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function getProductSku($storeId = null)
	{
		return $this->getConfig(self::PRODUCT_SKU, $storeId);
	}

	/**
	 * Saves access token for authorization bearer into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setAccessToken($value, $storeId = 0)
	{
		return $this->writeConfig(self::ACCESS_TOKEN, $value, $storeId);
	}
	/**
	 * Saves X into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setStoreCode($value, $storeId = 0)
	{
		return $this->writeConfig(self::STORE_CODE, $value, $storeId);
	}

	/**
	 * Saves synchronization start date into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setStartDate($value, $storeId = 0)
	{
		return $this->writeConfig(self::START_DATE, $value, $storeId);
	}

	/**
	 * Saves last synchronized order increment ID into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setIncrementId($value, $storeId = 0)
	{
		return $this->writeConfig(self::INCREMENT_ID, $value, $storeId);
	}

	/**
	 * Saves last synchronized order ID into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setOrderId($value, $storeId = 0)
	{
		return $this->writeConfig(self::ORDER_ID, $value, $storeId);
	}

	/**
	 * Saves order prefix into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setOrderPrefix($value, $storeId = 0)
	{
		return $this->writeConfig(self::ORDER_PREFIX, $value, $storeId);
	}

	/**
	 * Saves product sku into module config value.
	 * @param string $value
	 * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $storeId
	 */
	public function setProductSku($value, $storeId = 0)
	{
		return $this->writeConfig(self::PRODUCT_SKU, $value, $storeId);
	}
}