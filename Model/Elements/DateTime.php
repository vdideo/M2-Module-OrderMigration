<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Model\Elements;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

class DateTime extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * DateTime constructor.
	 * 
	 * @param Context $context
	 * @param Registry $coreRegistry
	 * @param array $data
	 */
	public function __construct(
		Context  $context,
		Registry $coreRegistry,
		array    $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		$element->setDateFormat(\Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
		$element->setTimeFormat("HH:mm:ss"); //set date and time as per requirment
		return parent::render($element);
	}
}
