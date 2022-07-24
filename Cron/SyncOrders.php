<?php
namespace Nooe\M2Connector\Cron;


class SyncOrders
{
    /**
     * @var \Nooe\M2Connector\Helper\Data
     */
    protected $helperData;

     /**
     * @var \Nooe\M2Connector\Service\OrderService
     */
    protected $orderService;

    /**
     * @param \Nooe\M2Connector\Service\OrderService $orderService
     */
    public function __construct(
        \Nooe\M2Connector\Helper\Data $helperData,
        \Nooe\M2Connector\Service\OrderService $orderService 
    ) {
        
        $this->helperData = $helperData;
        $this->orderService = $orderService;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $increment = $this->helperData->getIncrementId();
        $storeCode = $this->helperData->getStoreCode();
        $this->orderService->sync($increment, $storeCode);
    }
}
