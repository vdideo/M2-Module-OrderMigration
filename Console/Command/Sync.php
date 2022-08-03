<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      Tun2U Team <dev@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html  GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Sync extends Command
{

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManagerInterface;

	/**
	 * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
	 */
	protected $_customerInterfaceFactory;

	/**
	 * @var \Magento\Framework\Encryption\EncryptorInterface
	 */
	protected $_encryptorInterface;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $_customerRepositoryInterface;

	/**
	 * @var \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * @var \Magento\Customer\Api\AddressRepositoryInterface
	 */
	protected $_addressRepository;

	/**
	 * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
	 */
	protected $_addressDataFactory;

	/**
	 * @var \Magento\Framework\Api\SearchCriteriaBuilder 
	 */
	protected $_searchCriteriaBuilder;

	/**
	 * @var \Magento\Framework\Api\FilterBuilder
	 */
	protected $_filterBuilder;

	/**
	 * @var \Magento\Framework\App\State
	 */
	private $state;

	/**
	 * @var \Magento\Framework\Registry
	 */
	private $registry;

	/**
	 * @var orderService
	 */
	private $orderService;

    protected $moduleResource;


	public function __construct(
        \Magento\Framework\Module\ResourceInterface $moduleResource,
		\Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
		\Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
		\Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
		\Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
		\Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
		\Magento\Framework\Api\FilterBuilder $filterBuilder,
		\Magento\Framework\App\State $state,
		\Magento\Framework\Registry $registry,
		\Nooe\M2Connector\Service\OrderService $orderService
	) {
		$state->setAreaCode('adminhtml');
		$registry->register('isSecureArea', true);

        $this->moduleResource = $moduleResource;
		$this->_storeManagerInterface = $storeManagerInterface;
		$this->_customerInterfaceFactory = $customerInterfaceFactory;
		$this->_encryptorInterface = $encryptorInterface;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->_customerFactory = $customerFactory;
		$this->_addressRepository = $addressRepository;
		$this->_addressDataFactory = $addressDataFactory;
		$this->_searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->_filterBuilder = $filterBuilder;
		$this->state = $state;
		$this->registry = $registry;
		$this->orderService = $orderService;
		parent::__construct();
	}


	private function printHeading()
	{
		$version = $this->moduleResource->getDbVersion('Nooe_M2Connector');

		echo "  _   _  ___   ___  _____    ____ ___  _   _ _   _ _____ ____ _____ ___  ____  ";
		echo " | \ | |/ _ \ / _ \| ____|  / ___/ _ \| \ | | \ | | ____/ ___|_   _/ _ \|  _ \ ";
		echo " |  \| | | | | | | |  _|   | |  | | | |  \| |  \| |  _|| |     | || | | | |_) |";
		echo " | |\  | |_| | |_| | |___  | |__| |_| | |\  | |\  | |__| |___  | || |_| |  _ < ";
		echo " |_| \_|\___/ \___/|_____|  \____\___/|_| \_|_| \_|_____\____| |_| \___/|_| \_\\";
		echo "                                                                               v" . $version . "\n";
		echo "\n\n\n";

		echo "Sync\n\n";
	}

	private function show_status($done, $total, $size = 30)
	{
		static $start_time;

		// if we go over our bound, just ignore it
		if ($done > $total) {
			return;
		}

		if (empty($start_time)) {
			$start_time = time();
		}
		$now = time();

		$perc = (float)($done / $total);

		$bar = floor($perc * $size);

		$status_bar = "\r[";
		$status_bar .= str_repeat("=", $bar);
		if ($bar < $size) {
			$status_bar .= ">";
			$status_bar .= str_repeat(" ", $size - $bar);
		} else {
			$status_bar .= "=";
		}

		$disp = number_format($perc * 100, 0);

		$status_bar .= "] $disp%  $done/$total";

		$rate = ($now - $start_time) / $done;
		$left = $total - $done;
		$eta = round($rate * $left, 2);

		$elapsed = $now - $start_time;

		$status_bar .= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

		echo "$status_bar  ";

		flush();

		// when done, send a newline
		if ($done == $total) {
			echo "\n\n\n\n";
		}
	}

	protected function configure()
	{
		$this->setName('nooe:sync')
			->setDescription('Sync')
			->addOption('action', "action", InputOption::VALUE_OPTIONAL, "Specific Action")
			->addOption('increment', "increment", InputOption::VALUE_OPTIONAL, "Specific Increment Id");
		parent::configure();
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln($this->printHeading());

		$increment = null;
		$store = null;

		try {
			if ($input->getOption('action')) {
				$action = $input->getOption('action');
			}

			if ($input->getOption('increment')) {
				$increment = $input->getOption('increment');
			}

			switch ($action) {

				case 'order':

					$this->orderService->sync($increment);
					break;
			}
		} catch (\InvalidArgumentException $e) {
			$output->writeln('<error>Invalid argument.</error>');
		}
	}
}