<?php

declare(strict_types=1);

namespace Nooe\M2Connector\Service;

use Nooe\M2Connector\Model\Order;

/**
 * Class Order
 */

class OrderService
{
    private $order;

    public function __construct(
        Order $order
    ) {
        $this->order = $order;
    }

    public function sync($incrementId = null, $storeId = null)
    {
        $this->order->getList($incrementId, $storeId);
    }
}
