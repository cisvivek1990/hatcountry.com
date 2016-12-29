<?php
class MT_Exitoffer_Block_Popup extends Mage_Core_Block_Template
{
    private $__popup = null;

    private $__campaign = null;

    public function getPopup()
    {
        if ($this->__popup === null) {
            $this->__popup = $this->getCampaign()->getPopup();
        }
        return $this->__popup;
    }

    public function getCampaign()
    {
        if ($this->__campaign  === null) {
            $campaignModel = Mage::getSingleton('exitoffer/campaign');
            $this->__campaign = $campaignModel->getCurrent();
        }
        return $this->__campaign ;
    }


    public function getCookieLifeTime()
    {
        if (Mage::getStoreConfig('exitoffer/general/dev'))
            return 0.00001;
        $lifeTime = $this->getCampaign()->getCookieLifetime();
        if ($lifeTime == 0) {
            $lifeTime = 100*365;
        }

        return $lifeTime;
    }

    public function isActivePopUp()
    {
        if (Mage::getStoreConfig('exitoffer/general/is_active') != 1) {
           return false;
        }

        $campaign = Mage::getSingleton('exitoffer/campaign');
        if (!$campaign->getCurrent()) {
            return false;
        }

        return true;
    }

    public function getTheme()
    {
        $theme = $this->getPopup()->getTheme();
        return $theme;
    }

    public function getText()
    {
        return Mage::getStoreConfig('exitoffer/popup/message');
    }

    public function getPopupContentHtml()
    {
        return $this->getChildHtml('popup_theme_'.$this->getTheme());
    }

    public function getConfig()
    {
        $campaign = $this->getCampaign();
        $config = array(
            'actionUrl' => $this->getActionUrl(),
            'translate' => Mage::getStoreConfig('exitoffer/translate'),
            'layerClose' => $campaign->getLayerClose(),
            'showInLast' => $campaign->getShowInLastTab(),
            'cookieName' => $campaign->getCookieName(),
            'cookieLifeTime' => $this->getCookieLifeTime(),
            'campaignId' => $campaign->getId(),
            'isMobile' => $this->getIsMobile(),
            'mobileTrigger' => $campaign->getMobileTrigger(),
        );

        return $config;
    }

    public function getActionUrl()
    {
        $url = '';
        $popup = $this->getPopup();
        if ($popup->getContentType() == MT_Exitoffer_Model_Popup::CONTENT_TYPE_NEWSLETTER_SUBSCRIPTION_FORM) {
            $url = Mage::getUrl('exitoffer/subscriber/new/');
        }

        if ($popup->getContentType() == MT_Exitoffer_Model_Popup::CONTENT_TYPE_YES_NO) {
            $url = Mage::getUrl('exitoffer/index/coupon/');
        }

        if ($popup->getContentType() == MT_Exitoffer_Model_Popup::CONTENT_TYPE_CONTACT_FORM) {
            $url = Mage::getUrl('exitoffer/index/contact/');
        }
        return str_replace(array('http:', 'https:'), '', $url);
    }

    public function getConfigJs()
    {
        return json_encode($this->getConfig());
    }

    public function getContentType()
    {
        return $this->getPopup()->getContentType();
    }

    public function getShowInLast()
    {
        return Mage::getStoreConfig('exitoffer/popup/showinlast');
    }

    public function getStaticBlockHtml()
    {
        $id = $this->getPopup()->getStaticBlockId();
        if (!is_numeric($id)) {
            Mage::helper('exitoffer')->log('Static block is not assigned to popup. MT_Exitoffer_Block_Popup::getStaticBlockHtml');
            return '';
        }

        return Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($id)->toHtml();
    }


    public function getIsMobile()
    {
        $is_mobile = '0';

        if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $is_mobile=1;
        }

        if (isset($_SERVER['HTTP_ACCEPT'])) {
            if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
                $is_mobile=1;
            }
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
            $mobile_agents = array('w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');

            if(in_array($mobile_ua,$mobile_agents)) {
                $is_mobile=1;
            }
        }

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
                $is_mobile=1;
            }
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
                $is_mobile=0;
            }
        }

        return $is_mobile;
    }
}

