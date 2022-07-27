<?php
namespace Nooe\M2Connector\Cron;


class SyncOrders
{
     /**
     * @var \Nooe\M2Connector\Service\OrderService
     */
    protected $orderService;

    /**
     * @param \Nooe\M2Connector\Service\OrderService $orderService
     */
    public function __construct(
        \Nooe\M2Connector\Service\OrderService $orderService 
    ) {
        
        $this->orderService = $orderService;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->orderService->sync();
    }
}
