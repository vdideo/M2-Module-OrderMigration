<?php

namespace Nooe\M2Connector\Api;

interface OrderInterface
{

    public function create($order);
    
    public function getList($incrementId = null, $storeId = null);
}
