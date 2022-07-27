<?php

declare(strict_types=1);

namespace Nooe\M2Connector\Service;

/**
 * Class Order
 */

class OrderService
{
    private $order;

    protected $_storeManager;
    protected $_product;
    protected $_formkey;
    protected $quote;
    protected $quoteManagement;
    protected $customerFactory;
    protected $customerRepository;
    protected $orderService;
    protected $productFactory;
    protected $configData;
    private $logger;

    public function __construct(
        \Nooe\M2Connector\Model\Order $order,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Nooe\M2Connector\Helper\Data $configData,
        \Nooe\M2Connector\Logger\Logger $logger
    ) {
        $this->order = $order;
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->productFactory = $productFactory;
        $this->orderService = $orderService;
        $this->configData = $configData;
        $this->logger = $logger;
    }

    public function sync($incrementId = null, $storeId = null)
    {
        // get orders from remote Magento
        $orders = $this->order->getList($incrementId, $storeId);

        // sync logic
        if (count((array)$orders)) {
            foreach ($orders as $key => $order) {

                try {
                    $items = array();

                    foreach($order->items as $item) {
                        $product = $this->productFactory->create();

                        // controllo prima se esiste gia' un prodotto con questo sku
                        $productId = $product->getIdBySku($item->sku);

                        // TODO verificare se quantita' è disponibile e status inventario è disponibile
                        // TODO se non c'è loggare

                        if ($productId) {
                            $cartItem['product_id'] = $productId;
                            $cartItem['qty'] = $item->qty_ordered;
                            $cartItem['price'] = $item->row_total_incl_tax;
                            $items[] = $cartItem;
                        } else {
                            // todo sku non esiste, segnalare errore
                            $this->logger->error("ORDER: " . $order->increment_id . " - Lo SKU " . $item->sku . " non esiste");
                        }
                    }

                    $address = $order->extension_attributes->shipping_assignments[0]->shipping->address;

                    $localOrder=[
                        'currency_id'  => $order->order_currency_code,
                        'email'        => $order->customer_email,
                        'shipping_address' =>[
                            'firstname'    => $address->firstname,
                            'lastname'     => $address->lastname,
                            'street' => $address->street[0],
                            'city' => $address->city,
                            'country_id' => $address->country_id,
                            'region' => isset($address->region) ? $address->region : '',
                            'postcode' => $address->postcode,
                            'telephone' => $address->telephone,
                            'save_in_address_book' => 0 // TODO lo devo salvare?
                        ],
                        'increment_id' => $order->increment_id,
                        'order_id' => $order->entity_id,
                        'order_date' => $order->created_at,
                        'items'=> $items
                    ];

                    $result = $this->createMageOrder($localOrder); // TODO gestire eventuali errori
                } catch (\Exception $e) {
                    $this->logger->error("ORDER: " . $order->increment_id . " - ".$e->getMessage());
//                    echo "Errore ".$key."\n";
//                    echo $e->getMessage()."\n\n";
//                    var_dump($order->extension_attributes->shipping_assignments[0]->shipping->address);
//                    echo "\n\n";
                }


            }
        }
    }



    private function createMageOrder($orderData) {
        $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customet by email address

        $guest = false;
        if(!$customer->getEntityId()) {
            $guest = true;
        }
        $quote = $this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        $quote->setCurrency();

        if ($guest) {
            // Set Customer Data on Qoute, Do not create customer.
            $quote->setCustomerFirstname($orderData['shipping_address']['firstname']);
            $quote->setCustomerLastname($orderData['shipping_address']['lastname']);
            $quote->setCustomerEmail($orderData['email']);
            $quote->setCustomerIsGuest(true);
        } else {
            // if you have allready buyer id then you can load customer directly
            $customer= $this->customerRepository->getById($customer->getEntityId());
            $quote->assignCustomer($customer); //Assign quote to customer
        }

        //add items in quote
        foreach($orderData['items'] as $item){
            $product = $this->_product->load($item['product_id']);
            $quote->addProduct(
                $product,
                intval($item['qty'])
            );
        }

        //Set Address to quote
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);

        // Collect Rates and Set Shipping & Payment Method

        //$shippingAddress = $quote->getShippingAddress();
        $quote->getShippingAddress()->setFreeShipping(true);
        $quote->getShippingAddress()->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('freeshipping_freeshipping'); //shipping method*/

        // TODO settare nooe_shipping e prezzo di spedizione

        //$shippingAddress->setShippingMethod('freeshipping_freeshipping'); // nooe_shipping
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
        if($order->getEntityId()){
            $prefix = (string)$this->configData->getOrderPrefix();
            $incrementId = trim($prefix).$order->getIncrementId(); // get original increment ID
            $success = $order->setIncrementId($incrementId)->save(); // TODO settare date ordine
            //var_dump($success);exit;
            $result['order_id']= $order->getRealOrderId();

            if ($success) {
                $this->configData->setStartDate($orderData['order_date']);
                $this->configData->setIncrementId(trim($prefix) . $orderData['increment_id']);
                $this->configData->setOrderId($orderData['order_id']);
            }


        }else{
            $result=['error'=>1,'msg'=>'Error in order creation'];
        }

        return $result;
    }
}
