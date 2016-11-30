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
 
class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_New_Tabs_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $isElementDisabled = false;

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('affiliate_');
		
        $fieldset = $form->addFieldset('general_fieldset', array('legend' => $this->__('General'), 'class' => 'fieldset-wide'));
		
        $html = '';
        $types = Mage::getSingleton('affiliate/affiliate')->getTypes();
        foreach($types as $type){
            $html .= '<div class="affilate-item">
                    <input type="radio" name="type_id" value="'.$type->getId().'" id="affiliate_type_id'.$type->getId().'" />
                    <label title="'.htmlspecialchars($type->getName()).'" for="affiliate_type_id'.$type->getId().'" style="cursor:pointer;">
                        '. (($type->getId() == 1) ? '<span class="custom-label">'.$type->getName().'</span>' : '<img style="vertical-align:middle;" src="'.$this->getSkinUrl('images/plumrocket/affiliate/type'.$type->getId().'.png').'" />') .'
                    </label>
                </div>';
        }
	
		$fieldset->addField('type_id', 'radios', array(
			'name'      => 'type_id',
            'label'     => $this->__('Select Affiliate Network'),
            'title'     => $this->__('Select Affiliate Network'),
            //'required'  => true,
            'disabled'  => $isElementDisabled,
			//'values'	=> $values,
			'value'		=> 1,
			'after_element_html'	=> $html.'
				<br/><br/>
				<button type="button" class="scalable save" onclick="editForm.submit();" style=""><span>'.$this->__('Continue').'</span></button>
			',

        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('General Settings');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

}
