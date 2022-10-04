<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Cron;


class SyncOrders
{
	/**
	 * @var \Tun2U\OrderMigration\Service\OrderService
	 */
	protected $orderService;

	/**
	 * SyncOrders constructor.
	 * 
	 * @param \Tun2U\OrderMigration\Service\OrderService $orderService
	 */
	public function __construct(
		\Tun2U\OrderMigration\Service\OrderService $orderService
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
