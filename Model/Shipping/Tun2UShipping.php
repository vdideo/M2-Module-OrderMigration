<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Model\Shipping;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Tun2UShipping extends AbstractCarrier implements CarrierInterface
{
	/**
	 * @var string
	 */
	protected $_code = 'tun2u_shipping';


	/**
	 * @var \Magento\Shipping\Model\Rate\ResultFactory
	 */
	protected $_rateResultFactory;

	/**
	 * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
	 */
	protected $_rateMethodFactory;

	/**
	 * Tun2UShipping constructor.
	 * 
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
	 * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
		\Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
		array $data = []
	) {
		$this->_rateResultFactory = $rateResultFactory;
		$this->_rateMethodFactory = $rateMethodFactory;
		parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
	}

	/**
	 * @param RateRequest $request
	 * @return bool|Result
	 */
	public function collectRates(RateRequest $request)
	{
		if (!$this->getConfigFlag('active')) {
			return false;
		}

		/** @var \Magento\Shipping\Model\Rate\Result $result */
		$result = $this->_rateResultFactory->create();

		/** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
		$method = $this->_rateMethodFactory->create();

		$method->setCarrier($this->_code);
		$method->setCarrierTitle($this->getConfigData('title'));


		$method->setMethod(strtolower('0001'));
		$method->setMethodTitle($this->getConfigData('name'));

		$amount = 0; // is overridden in Order model

		$method->setPrice($amount);
		$method->setCost($amount);

		$result->append($method);

		return $result;
	}

	/**
	 * Returns allowed shipping methods
	 *
	 * @return array
	 */
	public function getAllowedMethods()
	{
		return [$this->_code => $this->getConfigData('name')];
	}
}
