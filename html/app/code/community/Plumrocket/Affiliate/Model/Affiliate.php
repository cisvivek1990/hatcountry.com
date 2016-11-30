<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php

class Plumrocket_Affiliate_Model_Affiliate extends Plumrocket_Affiliate_Model_Affiliate_Abstract
{

	public function load($id, $field = null)
    {
		parent::load($id, $field);
        return $this->getTypedModel();
    }

    public function getTypedModel($typeId = null){
		$type = $this->getType($typeId);
		$typeModel = Mage::getModel('affiliate/affiliate_'.ucfirst($type->getKey()));
		if ($typeModel){
			return $typeModel->simulateLoad($this);
		}
		return $this;
	}

}
