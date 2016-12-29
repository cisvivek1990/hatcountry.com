<?php
/**
 * Class Sellbrite_Api_Model_Credentials
 *
 * Data Model for API credentials
 */
class Sellbrite_Api_Model_Credentials
{
    const AUTH_NAME = 'Sellbrite';

    /**
     * Sellbrite end point URL
     *
     * @var string
     */
    private $endPointURL;

    /**
     * Merchant API key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Get SOAP user
     *
     * @var string
     */
    protected $soapUser;

    public function __construct()
    {
        $this->apiKey = Mage::getStoreConfig('sellbrite_api/config/api_key');
        $apiUser = Mage::getModel('api/user');
        $apiUser->loadByUsername(self::AUTH_NAME);
        $this->soapUser = $apiUser->getUsername();
        $this->endPointURL = (string) Mage::getConfig()->getNode('default/sellbrite_endpoint_url');
    }

    /**
     * Get API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get Soap User Name
     *
     * @return string
     */
    public function getSoapUser()
    {
        return $this->soapUser;
    }

    /**
     * Get end point URL
     *
     * @return string
     */
    public function getEndPointURL()
    {
        return $this->endPointURL;
    }
}