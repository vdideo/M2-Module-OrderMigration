<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Model;

use Nooe\M2Connector\Api\OrderInterface;

class Order implements OrderInterface
{
	/**
	 * API request endpoint
	 */
	const API_REQUEST_ENDPOINT = 'orders';

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
	 * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $customerFactory;

	/**
	 * @var \Magento\Quote\Model\QuoteFactory
	 */
	protected $quote;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepository;

	/**
	 * @var \Magento\Catalog\Model\Product
	 */
	protected $_product;

	/**
	 * @var \Magento\Quote\Model\QuoteManagement 
	 */
	protected $quoteManagement;

	/**
	 * @var \Magento\Quote\Model\Quote\Address\Rate
	 */
	protected $rate;

	/**
	 * @var \Nooe\M2Connector\Helper\Data
	 */
	protected $configData;

	public function __construct(
		\Nooe\M2Connector\Helper\Data $helperData,
		\Nooe\M2Connector\Model\Connector $connector,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Quote\Model\QuoteFactory $quote,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Catalog\Model\Product $product,
		\Magento\Quote\Model\QuoteManagement $quoteManagement,
		\Magento\Quote\Model\Quote\Address\Rate $rate,
		\Nooe\M2Connector\Helper\Data $configData,
		\Nooe\M2Connector\Logger\Logger $logger
	) {
		$this->helperData = $helperData;
		$this->connector = $connector;
		$this->_storeManager = $storeManager;
		$this->customerFactory = $customerFactory;
		$this->quote = $quote;
		$this->customerRepository = $customerRepository;
		$this->_product = $product;
		$this->quoteManagement = $quoteManagement;
		$this->rate = $rate;
		$this->configData = $configData;
		$this->logger = $logger;
	}

	public function create($order)
	{
		$store = $this->_storeManager->getStore();
		$websiteId = $this->_storeManager->getStore()->getWebsiteId();
		$customer = $this->customerFactory->create();
		$customer->setWebsiteId($websiteId);
		$customer->loadByEmail($order['email']); // load customet by email address

		$guest = false;
		if (!$customer->getEntityId()) {
			$guest = true;
		}
		$quote = $this->quote->create(); //Create object of quote
		$quote->setStore($store); //set store for which you create quote
		$quote->setCurrency();

		if ($guest) {
			// Set Customer Data on Qoute, Do not create customer.
			$quote->setCustomerFirstname($order['shipping_address']['firstname']);
			$quote->setCustomerLastname($order['shipping_address']['lastname']);
			$quote->setCustomerEmail($order['email']);
			$quote->setCustomerIsGuest(true);
		} else {
			// if you have allready buyer id then you can load customer directly
			$customer = $this->customerRepository->getById($customer->getEntityId());
			$quote->assignCustomer($customer); //Assign quote to customer
		}

		//add items in quote
		foreach ($order['items'] as $item) {
			$product = $this->_product->load($item['product_id']);
			$quote->addProduct(
				$product,
				intval($item['qty'])
			);
		}

		//Set Address to quote
		$quote->getBillingAddress()->addData($order['shipping_address']);
		$quote->getShippingAddress()->addData($order['shipping_address']);

		// Collect Rates and Set Shipping & Payment Method

		$shippingRateCarrier = 'nooeshipping';
		$shippingRateCarrierTitle = 'NOOE SHIPPING';
		$shippingRateCode = 'nooeshipping';
		$shippingRateMethod = 'nooeshipping';
		$shippingRatePrice = $order['shipping_amount'];
		$shippingRateMethodTitle = 'NOOE SHIPPING METHOD';

		$this->rate->setCarrier($shippingRateCarrier);
		$this->rate->setCarrierTitle($shippingRateCarrierTitle);
		$this->rate->setCode($shippingRateCode);
		$this->rate->setMethod($shippingRateMethod);
		$this->rate->setPrice($shippingRatePrice);
		$this->rate->setMethodTitle($shippingRateMethodTitle);
		$shippingAddress = $quote->getShippingAddress();
		$shippingAddress->setCollectShippingRates(true)
			->collectShippingRates()
			->setShippingMethod($shippingRateCode); //shipping method
		$quote->getShippingAddress()->addShippingRate($this->rate);

		$quote->setPaymentMethod('nooe_payments'); //payment method
		$quote->setInventoryProcessed(false); //not affect inventory
		$quote->save(); //Now Save quote and your quote is ready

		// Set Sales Order Payment
		$quote->getPayment()->importData(['method' => 'nooe_payments']);

		// Collect Totals & Save Quote
		$quote->collectTotals()->save();

		// Create Order From Quote
		$order = $this->quoteManagement->submit($quote);
		$order->setEmailSent(0);
		if ($order->getEntityId()) {
			$prefix = (string)$this->configData->getOrderPrefix();
			$incrementId = trim($prefix) . $order->getIncrementId(); // get original increment ID
			$success = $order->setIncrementId($incrementId)->save(); // TODO settare date ordine
			//var_dump($success);exit;
			$result['order_id'] = $order->getRealOrderId();

			if ($success) {
				$this->configData->setStartDate($order['order_date']);
				$this->configData->setIncrementId($order['increment_id']);
				$this->configData->setOrderId($order['order_id']);
			}
		} else {
			$result = ['error' => 1, 'msg' => 'Error in order creation'];
		}

		return $result;
	}

	public function getList($incrementId = null)
	{
		$orders         = array();
		$data           = array();
		$searchCriteria = array();
		$orderLimit     = 100;
		$stardDate      = $this->helperData->getStartDate();
		$storeId      	= $this->helperData->getStoreCode();

		$suckerInterval = ' +15 day';
		$fromDate       = date('Y-m-d H:i:s', strtotime($stardDate));
		$toDate         = date('Y-m-d H:i:s', strtotime($stardDate . $suckerInterval));

		if ($storeId) {
			/**
			 * Init Filters
			 */
			if (!is_null($incrementId)) {
				$searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][field]=store_id&';
				$searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][value]=' . $storeId . '&';
				$searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][condition_type]=eq&';
				$searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][field]=increment_id&';
				$searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][value]=' . $incrementId . '&';
				$searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][condition_type]=eq&';
				$searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][field]=status&';
				$searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][value]=complete&';
				$searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][condition_type]=eq&';
			} else {
				$searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][field]=store_id&';
				$searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][value]=' . $storeId . '&';
				$searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][condition_type]=eq&';
				$searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][field]=created_at&';
				$searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][value]=' . $fromDate . '&';
				$searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][condition_type]=gteq&';
				$searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][field]=created_at&';
				$searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][value]=' . $toDate . '&';
				$searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][condition_type]=lteq&';
				$searchCriteria[] = 'searchCriteria[filter_groups][3][filters][0][field]=status&';
				$searchCriteria[] = 'searchCriteria[filter_groups][3][filters][0][value]=complete&';
				$searchCriteria[] = 'searchCriteria[filter_groups][3][filters][0][condition_type]=eq&';
			}

			$searchCriteria[] = 'searchCriteria[pageSize]=' . $orderLimit . '&';
			$searchCriteria[] = 'searchCriteria[currentPage]=1';

			try {

				$allOrders = $this->connector->call(self::API_REQUEST_ENDPOINT, null, implode('', $searchCriteria));

				if ($allOrders && isset($allOrders->items) && count($allOrders->items)) {
					return $allOrders->items;

					// $totalOrderCount    = count((array)$orders);
					// $count              = 0;
					// foreach ($orders as $orderObj) {
					//     var_dump($orderObj);
					//     die();
					// }
				}
			} catch (\Exception $exception) {
				// TODO add log with error message
				var_dump($exception);
				die();
			}
		} else {
			die("missing store id in module configuration");
		}
	}
}