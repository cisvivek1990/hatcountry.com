<?php
/**
 * Install Sellbrite API data fixtures
 * Required entities:
 *  - SOAP API USE
 */
//======================= Predefined values ======================//
$authName = Sellbrite_Api_Model_Credentials::AUTH_NAME;
$adminEmail = 'magento@sellbrite.com';
//================================================================//
//II. Create SOAP API Credentials
//1. Generate API key
$apiKey = Mage::getStoreConfig('sellbrite_api/config/api_key');
if( !$apiKey || strlen($apiKey) < 32 ){
    $apiKey = md5(uniqid(rand(), true));
    $config = new Mage_Core_Model_Config();
    $config ->saveConfig('sellbrite_api/config/api_key', $apiKey, 'default', 0);
}

//2. Create API Role
/** @var Mage_Api_Model_Resource_Roles_Collection $roleCollection */
$roleCollection = Mage::getModel('api/roles')->getCollection();
$roleCollection->addFieldToFilter('role_name', $authName);
$roleCollection->setPageSize(1);
$apiRole = $roleCollection->fetchItem();
if (!$apiRole) {
    //create new role
    /** @var Mage_Api_Model_Roles $role */
    $apiRole = Mage::getModel("api/roles");
    $apiRole->setName($authName);
    $apiRole->setRoleType('G');
    $apiRole->save();

    //give "all" privileges to role
    /** @var Mage_Api_Model_Rules $rule */
    $rule = Mage::getModel("api/rules");
    $rule->setRoleId($apiRole->getId());
    $rule->setResources(array(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL));
    $rule->saveRel();
}

//3. Create SOAP Api User
//create user if it does not exist
$apiUser = Mage::getModel('api/user');
$apiUser->loadByUsername($authName);
if ($apiUser->getId()) {
    //update api key
    $apiUser->setApiKey($apiKey);
    $apiUser->save();
}else{
    /** @var Mage_Api_Model_User $apiUser */
    $apiUser = Mage::getModel('api/user')
        ->setData(array(
            'username'             => $authName,
            'firstname'            => $authName,
            'lastname'             => $authName,
            'email'                => $adminEmail,
            'api_key'              => $apiKey,
            'api_key_confirmation' => $apiKey,
            'is_active'            => 1,
            'user_roles'           => '',
            'assigned_user_role'   => '',
            'role_name'            => '',
            'roles'                => array($apiRole->getId()) // your created custom role
        ));
    $apiUser->save();

    //assign role to user
    $apiUser
        ->setRoleIds(array($apiRole->getId()))  // your created custom role
        ->setRoleUserId($apiUser->getUserId())
        ->saveRelations();
}