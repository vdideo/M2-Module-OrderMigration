<?php

namespace Nooe\M2Connector\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Sync extends Command
{

    protected $_storeManagerInterface;
    protected $_customerInterfaceFactory;
    protected $_encryptorInterface;
    protected $_customerRepositoryInterface;
    protected $_customerFactory;
    protected $_addressDataFactory;
    protected $_addressRepository;

    protected $_orderRepository;

    protected $_searchCriteriaBuilder;

    protected $_filterBuilder;

    private $stdTimezone;

    private $directoryList;

    private $state;

    private $orderService;

    //private $orderRepository;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone,
        \Magento\Framework\App\State $state,
        \Nooe\M2Connector\Service\OrderService $orderService
        //\Nooe\M2Connector\Model\Order $orderRepository
    ) {
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_customerInterfaceFactory = $customerInterfaceFactory;
        $this->_encryptorInterface = $encryptorInterface;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerFactory = $customerFactory;
        $this->directoryList = $directoryList;
        $this->_addressRepository = $addressRepository;
        $this->_addressDataFactory = $addressDataFactory;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->stdTimezone = $stdTimezone;
        $this->state = $state;
        $this->orderService = $orderService;
        //$this->orderRepository = $orderRepository;
        parent::__construct();
    }


    private function printHeading()
    {
        ob_start();

        echo 'Sync';
    }

    protected function configure()
    {
        $this->setName('nooe:sync')
            ->setDescription('Sync')
            ->addOption('action', "action", InputOption::VALUE_OPTIONAL, "Specific Action")
            ->addOption('increment', "increment", InputOption::VALUE_OPTIONAL, "Specific Increment Id")
            ->addOption('store', "store", InputOption::VALUE_OPTIONAL, "Specific Store Id");
        parent::configure();
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->printHeading());

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $increment = null;
        $store = null;

        try {
            if ($input->getOption('action')) {
                $action = $input->getOption('action');
            }

            if ($input->getOption('increment')) {
                $increment = $input->getOption('increment');
            }

            if ($input->getOption('store')) {
                $store = $input->getOption('store');
            }

            switch ($action) {

                case 'order':

                    $this->orderService->sync($increment, $store);

                    // if (count((Array)$orders)) {
                    //     foreach ($orders as $order) {

                    //     var_dump($order);

                    //     try {
                    //         $orderId = $this->orderRepository->create($order);

                    //         if ($orderId) {
                    //             $createdAt = $this->stdTimezone->date(new \DateTime($order->getCreatedAt()))->format('Y-m-d H:i:s');
                    //             $incrementId = $order->getIncrementId(); 

                    //             //TODO: update data e increment nei parametri del modulo
                    //         }

                    //     } catch (Exception $e) {
                    //         var_dump($e->getMessage());
                    //     }
                    // } 

                    break;
            }
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>Invalid argument.</error>');
        }
    }
}
