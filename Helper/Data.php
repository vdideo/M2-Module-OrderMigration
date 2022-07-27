<?php

namespace Nooe\M2Connector\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ACCESS_TOKEN = 'connector/connector_settings/access_token';
    const STORE_CODE = 'connector/connector_settings/store_code';
    const START_DATE = 'connector/connector_settings/start_date';
    const INCREMENT_ID = 'connector/connector_settings/increment_id';
    const ORDER_ID = 'connector/connector_settings/order_id';
    const ORDER_PREFIX = 'connector/connector_settings/order_prefix';


    private $_configWriter;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_configWriter = $configWriter;
    }

    private function getConfig($config_path, $store = null)
    {
        $store = $this->_storeManager->getStore($store);

        $result = $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $result;
    }

    private function setConfig($config_path, $value, $storeId = 0)
    {
        $this->_configWriter->save($config_path, $value, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
    }

    public function getAccessToken($storeId = null)
    {
        return $this->getConfig(self::ACCESS_TOKEN, $storeId);
    }

    public function getStoreCode($storeId = null)
    {
        return $this->getConfig(self::STORE_CODE, $storeId);
    }

    public function getStartDate($storeId = null)
    {
        return $this->getConfig(self::START_DATE, $storeId);
    }

    public function getIncrementId($storeId = null)
    {
        return $this->getConfig(self::INCREMENT_ID, $storeId);
    }

    public function getOrderId($storeId = null)
    {
        return $this->getConfig(self::ORDER_ID, $storeId);
    }

    public function getOrderPrefix($storeId = null)
    {
        return $this->getConfig(self::ORDER_PREFIX, $storeId);
    }

    public function setAccessToken($value, $storeId = 0)
    {
        return $this->setConfig(self::ACCESS_TOKEN, $value, $storeId);
    }

    public function setStoreCode($value, $storeId = 0)
    {
        return $this->setConfig(self::STORE_CODE, $value, $storeId);
    }

    public function setStartDate($value, $storeId = 0)
    {
        return $this->setConfig(self::START_DATE, $value, $storeId);
    }

    public function setIncrementId($value, $storeId = 0)
    {
        return $this->setConfig(self::INCREMENT_ID, $value, $storeId);
    }

    public function setOrderId($value, $storeId = 0)
    {
        return $this->setConfig(self::ORDER_ID, $value, $storeId);
    }

    public function setOrderPrefix($value, $storeId = 0)
    {
        return $this->setConfig(self::ORDER_PREFIX, $value, $storeId);
    }
}
