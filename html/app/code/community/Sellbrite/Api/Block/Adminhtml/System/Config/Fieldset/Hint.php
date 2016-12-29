<?php
/**
 * Class Sellbrite_Api_Block_Adminhtml_System_Config_Fieldset_Hint
 *
 * Sellbrite API fieldset renderer
 */
class Sellbrite_Api_Block_Adminhtml_System_Config_Fieldset_Hint
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Minimal supported Magento version
     *
     * @var string
     */
    protected $minVersion = '1.7.0.0';

    /**
     * Fieldset render template
     *
     * @var string
     */
    protected $_template = 'sellbrite/api/system/config/fieldset/hint.phtml';

    /**
     * @var Sellbrite_Api_Model_Credentials
     */
    protected $credentials;

    /**
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct($args);
        $this->credentials = Mage::getSingleton('sellbrite_api/credentials');
    }

    /**
     * Render fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    /**
     * Get min version
     *
     * @return string
     */
    public function getMinVersion()
    {
        return $this->minVersion;
    }

    /**
     * Check if current Magento version is supported by extension
     *
     * @return bool
     */
    public function isSupportedVersion()
    {
        $version = $this->getMagentoVersion();
        return version_compare($version, $this->minVersion, '>=');
    }

    /**
     * Get current Magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return Mage::getVersion();
    }

    /**
     * Get SOAP user
     *
     * @return string
     */
    public function getSoapUser()
    {
        return $this->credentials->getSoapUser();
    }

    /**
     * Get API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->credentials->getApiKey();
    }

    /**
     * Get Sellbrite end point URL
     *
     * @return string
     */
    public function getEndPointUrl()
    {
        return $this->credentials->getEndPointUrl();
    }
}