<?php

/**
 * @category    Nooe
 * @package     Nooe_M2_Connector
 * @author      NOOE Team <dev@nooestores.com>
 * @copyright   Copyright(c) 2022 NOOE (https://www.nooestores.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Nooe\M2Connector\Model\Elements;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class Increment extends Field
{
	/**
	 * {@inheritdoc}
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
		$element->setDisabled('disabled');
		return $element->getElementHtml();
	}
}
