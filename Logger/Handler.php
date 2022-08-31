<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Logger;

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
	protected $fileName = '/var/log/nooe_connector.log';
}
