<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

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