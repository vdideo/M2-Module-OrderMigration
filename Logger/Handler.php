<?php

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
