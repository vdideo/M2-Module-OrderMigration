<?php

/**
 * @category    Nooe
 * @package     Nooe_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\Connector\Logger;

use \Nooe\Connector\Helper\Data as ConfigData;

class Logger extends \Monolog\Logger
{

	/**
	 * @var \Nooe\Connector\Helper\Data
	 */
	protected $configData;

	/**
	 * @psalm-param array<callable(array): array> $processors
	 *
	 * @param string             $name       The logging channel, a simple descriptive name that is attached to all log records
	 * @param HandlerInterface[] $handlers   Optional stack of handlers, the first one in the array is called first, etc.
	 * @param callable[]         $processors Optional array of processors
	 * @param DateTimeZone|null  $timezone   Optional timezone, if not provided date_default_timezone_get() will be used
	 * @param \Nooe\Connector\Helper\Data $configData
	 */
	public function __construct(string $name, array $handlers = [], array $processors = [], ?DateTimeZone $timezone = null, ConfigData $configData)
	{
		$this->name = $name;
		$this->setHandlers($handlers);
		$this->processors = $processors;
		$this->timezone = $timezone ?: new DateTimeZone(date_default_timezone_get() ?: 'UTC');
		$this->configData = $configData;
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
			return null;
		}

		if (isset(self::RFC_5424_LEVELS[$level])) {
			$level = self::RFC_5424_LEVELS[$level];
		}

		if ($this->detectCycles) {
			$this->logDepth += 1;
		}
		if ($this->logDepth === 3) {
			$this->warning('A possible infinite logging loop was detected and aborted. It appears some of your handler code is triggering logging, see the previous log record for a hint as to what may be the cause.');
			return false;
		} elseif ($this->logDepth >= 5) { // log depth 4 is let through so we can log the warning above
			return false;
		}

		try {
			$record = null;

			foreach ($this->handlers as $handler) {
				if (null === $record) {
					// skip creating the record as long as no handler is going to handle it
					if (!$handler->isHandling(['level' => $level])) {
						continue;
					}

					$levelName = static::getLevelName($level);

					$record = [
						'message' => $message,
						'context' => $context,
						'level' => $level,
						'level_name' => $levelName,
						'channel' => $this->name,
						'datetime' => $datetime ?? new DateTimeImmutable($this->microsecondTimestamps, $this->timezone),
						'extra' => [],
					];

					try {
						foreach ($this->processors as $processor) {
							$record = $processor($record);
						}
					} catch (Throwable $e) {
						$this->handleException($e, $record);

						return true;
					}
				}

				// once the record exists, send it to all handlers as long as the bubbling chain is not interrupted
				try {
					if (true === $handler->handle($record)) {
						break;
					}
				} catch (Throwable $e) {
					$this->handleException($e, $record);

					return true;
				}
			}
		} finally {
			if ($this->detectCycles) {
				$this->logDepth--;
			}
		}

		return null !== $record;
	}
}