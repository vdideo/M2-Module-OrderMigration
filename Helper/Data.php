<?php

namespace Nooe\M2Connector\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ACCESS_TOKEN = 'connector/connector_settings/access_token';
    const STORE_CODE = 'connector/connector_settings/store_code';
    const START_DATE = 'connector/connector_settings/start_date';
    const INCREMENT_ID = 'connector/connector_settings/increment_id';


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    private function getConfig($config_path, $store = null)
    {
        $store = $this->_storeManager->getStore($store);

        $result = $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $result;
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
}
