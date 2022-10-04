<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Logger;

use \Tun2U\OrderMigration\Helper\Data as ConfigData;
use \Monolog\DateTimeImmutable;
use DateTimeZone;

class Logger extends \Monolog\Logger
{

	/**
	 * @var \Tun2U\OrderMigration\Helper\Data
	 */
	protected $configData;

	/**
	 * @psalm-param array<callable(array): array> $processors
	 *
	 * @param string             $name       The logging channel, a simple descriptive name that is attached to all log records
	 * @param HandlerInterface[] $handlers   Optional stack of handlers, the first one in the array is called first, etc.
	 * @param callable[]         $processors Optional array of processors
	 * @param DateTimeZone|null  $timezone   Optional timezone, if not provided date_default_timezone_get() will be used
	 * @param ConfigData $configData
	 */
	public function __construct(string $name, array $handlers = [], array $processors = [], ?DateTimeZone $timezone = null, ConfigData $configData)
	{
		$this->configData = $configData;
		parent::__construct($name, $handlers, $processors, $timezone);
	}

	/**
	 * Adds a log record.
	 *
	 * @param  int               $level    The logging level (a Monolog or RFC 5424 level)
	 * @param  string            $message  The log message
	 * @param  mixed[]           $context  The log context
	 * @param  DateTimeImmutable $datetime Optional log date to log into the past or future
	 * @return bool              Whether the record has been processed
	 *
	 * @phpstan-param Level $level
	 */
	public function addRecord(int $level, string $message, array $context = [], DateTimeImmutable $datetime = null): bool
	{
		if (!$this->configData->getDebugMode()) {
			return false;
		}

		return parent::addRecord($level, $message, $context, $datetime);
	}
}