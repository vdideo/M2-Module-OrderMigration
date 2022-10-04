<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
	/**
	 * Logging level
	 * @var int
	 */
	protected $loggerType = Logger::ERROR;

	/**
	 * File name
	 * @var string
	 */
	protected $fileName = '/var/log/tun2u_order_migration.log';
}
