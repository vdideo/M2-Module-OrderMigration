<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

declare(strict_types=1);

namespace Nooe\M2Connector\Service;


class OrderService
{
	private $order;
	protected $productFactory;
	protected $configData;
	private $logger;

	public function __construct(
		\Nooe\M2Connector\Model\Order $order,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Nooe\M2Connector\Helper\Data $configData,
		\Nooe\M2Connector\Logger\Logger $logger
	) {
		$this->order = $order;
		$this->productFactory = $productFactory;
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

					foreach ($order->items as $item) {
						$product = $this->productFactory->create();

                        // check if product exist
                        $productId = $product->getIdBySku($item->sku);

                        if ($productId) {
                            // check if stock is available
                            $product->load($productId);
                            $stockItem = $product->getExtensionAttributes()->getStockItem();

                            if (!empty($stockItem)) {
                                if (!$stockItem->getIsInStock() || $stockItem->getQty() < $item->qty_ordered) {
                                    $this->logger->error("ORDER: " . $order->increment_id . " - quantità non disponibile per lo SKU " . $item->sku);
                                    die();
                                } else {
                                    $cartItem['product_id'] = $productId;
                                    $cartItem['qty'] = $item->qty_ordered;
                                    $cartItem['price'] = $item->row_total_incl_tax;
                                    $items[] = $cartItem;
                                }
                            } else {
                                $this->logger->error("ORDER: " . $order->increment_id . " - impossibile verificare la quantità per lo SKU " . $item->sku);
                            }
                        } else {
                            $this->logger->error("ORDER: " . $order->increment_id . " - Lo SKU " . $item->sku . " non esiste");
                            die();
                        }
                    }

					$address = $order->extension_attributes->shipping_assignments[0]->shipping->address;

					$localOrder = [
						'currency_id'  => $order->order_currency_code,
						'email'        => $order->customer_email,
						'shipping_address' => [
							'firstname'    => $address->firstname,
							'lastname'     => $address->lastname,
							'street' => $address->street[0],
							'city' => $address->city,
							'country_id' => $address->country_id,
							'region' => isset($address->region) ? $address->region : '',
							'postcode' => $address->postcode,
							'telephone' => $address->telephone,
							'save_in_address_book' => 0
						],
						'increment_id' => $order->increment_id,
						'order_id' => $order->entity_id,
						'order_date' => $order->created_at,
						'items' => $items,
                        'shipping_amount' => (float)$order->shipping_incl_tax
					];

					$result = $this->order->create($localOrder); // TODO gestire eventuali errori
				} catch (\Exception $e) {
					$this->logger->error("ORDER: " . $order->increment_id . " - " . $e->getMessage());
                    die();
				}
			}
		}
	}
}