<?php

namespace Nooe\M2Connector\Model;

use Nooe\M2Connector\Api\OrderInterface;
use Nooe\M2Connector\Helper\Data;
use Nooe\M2Connector\Model\Connector;

class Order implements OrderInterface
{
    /**
     * API request endpoint
     */
    const API_REQUEST_ENDPOINT = 'orders';

    private $helperData;
    private $connector;

    public function __construct(
        Data $helperData,
        Connector $connector
    ) {
        $this->helperData = $helperData;
        $this->connector = $connector;
    }

    public function create($order)
    {
        // TODO
    }

    public function getList($incrementId = null, $storeId = null)
    {
        $orders         = array();
        $data           = array();
        $searchCriteria = array();
        $orderLimit     = 100;
        $stardDate      = $this->helperData->getStartDate();
        $storeCode      = $this->helperData->getStoreCode();

        $suckerInterval = ' +15 day';
        $fromDate       = date('Y-m-d H:i:s', strtotime($stardDate));
        $toDate         = date('Y-m-d H:i:s', strtotime($stardDate . $suckerInterval));


        /**
         * Init Filters
         */
        if (!is_null($incrementId)) {
            $searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][field]=increment_id&';
            $searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][value]=' . $incrementId . '&';
            $searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][condition_type]=eq&';
        } else {
            $searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][field]=store_id&';
            $searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][value]=' . $storeCode . '&';
            $searchCriteria[] = 'searchCriteria[filter_groups][0][filters][0][condition_type]=eq&';
            $searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][field]=created_at&';
            $searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][value]=' . $fromDate . '&';
            $searchCriteria[] = 'searchCriteria[filter_groups][1][filters][0][condition_type]=gteq&';
            $searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][field]=created_at&';
            $searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][value]=' . $toDate . '&';
            $searchCriteria[] = 'searchCriteria[filter_groups][2][filters][0][condition_type]=lteq&';
        }

        $searchCriteria[] = 'searchCriteria[pageSize]=' . $orderLimit . '&';
        $searchCriteria[] = 'searchCriteria[currentPage]=1';

        try {

            $allOrders = $this->connector->call(self::API_REQUEST_ENDPOINT, null, implode('', $searchCriteria));

            if ($allOrders && isset($allOrders->items) && count($allOrders->items)) {
                $orders             = $allOrders->items;
                return $orders;

                // $totalOrderCount    = count((array)$orders);
                // $count              = 0;
                // foreach ($orders as $orderObj) {
                //     var_dump($orderObj);
                //     die();
                // }
            }
        } catch (Exception $exception) {
            // TODO add log with error message
            var_dump($exception);
            die();
        }
    }
}
