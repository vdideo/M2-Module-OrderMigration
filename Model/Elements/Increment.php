<?php

/**
 * @category    Tun2U
 * @package     Tun2U_OrderMigration
 * @author      Tun2U Team <info@tun2u.com>
 * @copyright   Copyright(c) 2022 Tun2U (https://www.tun2u.com)
 * @license     https://opensource.org/licenses/gpl-3.0.html GNU General Public License (GPL 3.0)
 */

namespace Tun2U\OrderMigration\Model\Elements;

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
