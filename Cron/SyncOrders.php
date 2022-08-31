<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Cron;


class SyncOrders
{
	/**
	 * @var \Nooe\M2Connector\Service\OrderService
	 */
	protected $orderService;

	/**
	 * SyncOrders constructor.
	 * 
	 * @param \Nooe\M2Connector\Service\OrderService $orderService
	 */
	public function __construct(
		\Nooe\M2Connector\Service\OrderService $orderService
	) {

		$this->orderService = $orderService;
	}

	/**
	 * Synchronize orders with other magento store.
	 *
	 * @return void
	 */
	public function execute()
	{
		$this->orderService->sync();
	}
}
