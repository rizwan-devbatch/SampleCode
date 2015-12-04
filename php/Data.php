<?php

class Admin_Brands_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getSessionModel() {

        $user = Mage::getSingleton('admin/session'); 
        $userId = $user->getUser()->getUserId();

        return $model = Mage::getModel("admin/brand")->load($userId, 'brand_user_id');

    }
    
    public function _getStoreId() {
        $user = Mage::getSingleton('admin/session');
        $adminuserId = $user->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole()->getData();
        $Brandmodel = Mage::getModel("admin/brand")->load($adminuserId, 'brand_user_id')->toArray();
        $role_name = $role_data['role_name'];
        if ($role_name == "Brand_Brand") {
            $storeId = $Brandmodel['brand_storeid'];
        }

        return $storeId;
    }
    public function _getWebsiteId() {
//        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $user = Mage::getSingleton('admin/session');
        $adminuserId = $user->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole()->getData();
        $Brandmodel = Mage::getModel("admin/brand")->load($adminuserId, 'brand_user_id')->toArray();
        $role_name = $role_data['role_name'];
        if ($role_name == "Brand_Brand") {
            $storeId = $Brandmodel['brand_store_webid'];
        }

        return $storeId;
    }
    
    public function _getUserRoleName() {
        
        $user = Mage::getSingleton('admin/session');
        $adminuserId = $user->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        
        return $role_data->getData('role_name');
    }
    
    public function _isAdministrator() {
        return ($this->_getUserRoleName()=='Administrators');
    }


    public function getProductBrand($StoreId){
        $BrandUser = Mage::getModel("admin/brand")->load($StoreId, 'brand_storeid')->toArray();
       
        $user_data = Mage::getModel('admin/user')->load($BrandUser['brand_user_id'])->getData();
        return array("userinfo" => $user_data, "brandinfo" => $BrandUser);
    }
    
    public function getBrandInfo($Params) {
        $BrandUser = Mage::getModel("admin/brand")->load($Params['brand'], 'brand_id')->toArray();

        $user_data = Mage::getModel('admin/user')->load($BrandUser['brand_user_id'])->getData();
        return array("userinfo" => $user_data, "brandinfo" => $BrandUser);
    }

}
