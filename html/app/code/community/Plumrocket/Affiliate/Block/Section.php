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
 * @copyright   Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php

class Plumrocket_Affiliate_Block_Section extends Plumrocket_Affiliate_Block_Section_Abstract
{

	protected function _toHtml()
	{
		if (!$this->isEnabled()){
			return;
		}

		$_section = $this->getSection();
		$getSectionIncludeonId = 'getSection'.ucfirst($_section).'IncludeonId';

		$_session = $this->_getSession();
		//$_session->setPlumrocketAffiliateRegisterSuccess(true); // for test delete it
		if ($_session->getPlumrocketAffiliateRegisterSuccess()){
			$this->addIncludeon('registration_success_pages');
			if ($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND){
				$_session->setPlumrocketAffiliateRegisterSuccess(false);
			}
		}
		if ($_session->getPlumrocketAffiliateLoginSuccess()){
			$this->addIncludeon('login_success_pages');
			if ($_section == Plumrocket_Affiliate_Model_Affiliate_Abstract::SECTION_BODYEND){
				$_session->setPlumrocketAffiliateLoginSuccess(false);
			}
		}

		$html = '';
		foreach($this->getPageAffiliates() as $affiliate){
			$_includeon = $this->getARegistry()->getIncludeon($affiliate->$getSectionIncludeonId());
			if (!$_includeon){
				continue;
			}

			if (!$this->inIncludeon($_includeon->getKey())){
				continue;
			}

			$html .= $affiliate->getLibraryHtml($_section, $this->getIncludeon());
			$html .= $affiliate->getCodeHtml($_section, $this->getIncludeon());
		}

		return $html;
	}

}
