<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Admin_IndexController extends Mage_Adminhtml_Controller_Action {

    protected function _construct() {
        // your logic of construct
    }

    public function indexAction() {
        $sessionUser = Mage::helper('whibrand')->getSessionModel();
        $params = $this->getRequest()->getParams();
        $collection = Mage::getModel('brand/assign')->getCollection();
        $collection->getSelect()->join(array('celeb_table' => $collection->getTable('whohasitadmin/celebrity')), 'main_table.assign_celebid = celeb_table.celebrity_id', array('celeb_table.*'));
        $collection->getSelect()->join(array('user_table' => $collection->getTable('admin/user')), 'celeb_table.celebrity_user_id = user_table.user_id', array('user_table.*'));
        $collection->addFieldToFilter(array('main_table.assign_brandid'), array($sessionUser->getId()));
        if ($params['status'] == '0') {
            $collection->addFieldToFilter(array('main_table.assign_new'), array(1));
            $collection->addFieldToFilter(array('main_table.assign_status'), array(0));
        } else if ($params['status'] == 'all') {
            $collection->addFieldToFilter(array('main_table.celeb_action'), array(1));
        } else {
            $collection->addFieldToFilter(array('main_table.assign_status'), array($params['status']));
        }


        $collection->addFieldToFilter(array('main_table.assign_by'), array('c'));
        $collection->getSelect()->columns('count(assign_celebid) AS assign_celeb_count');
        $collection->setOrder('assign_date', 'asc');
        $collection->getSelect()->group(array('assign_collectionid'));

        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setForcedTitle($this->__('Celeb Request - Home'));

        $data['data']['new_requests'] = $collection->toArray();
        $data['params'] = $params;
        $block = Mage::app()->getLayout()->getBlock('whibrand_celebreq_index');

        if ($block) {
            $block->setData($data);
        }
        $this->renderLayout();
    }

    public function individualcollectionAction() {
        $params = $this->getRequest()->getParams();
        $data = array();

        $sessionUser = Mage::helper('whibrand')->getSessionModel();
        if ($this->getRequest()->getPost()) {
            $celebs = $params['chk_confirm'];
            foreach ($celebs as $index => $celeb) {
                $celebData = explode("-", $celeb);

                $collection = Mage::getModel('brand/assign')->getCollection()
                        ->addFieldToFilter('assign_collectionid', $params['id'])
                        ->addFieldToFilter('assign_celebid', $celebData[0])
                        ->addFieldToFilter('assign_brandid', $sessionUser->getId())
                        ->addFieldToFilter('assign_by', 'b')
                        ->addFieldToFilter('assign_status', '1')
                        ->addFieldToFilter('assign_new', '1');
                $data = array("assign_status" => 2); // set to open order

                foreach ($collection as $index => $items) {
                    $items->addData($data);
                    $items->save();
                }
                $celebArray[$celebData[0]]['request'] = $celebData[1];
            }

            $data['data']['status'] = true;
            $data['data']['confirm_request'] = $celebArray;
        }

        $this->loadLayout();

        $this->getLayout()->getBlock('root')->setForcedTitle($this->__('Celeb Request - Confirm & Pay'));
        $block = Mage::app()->getLayout()->getBlock('ind_collection_confirm');
        $data['data']['post'] = $params;


        if ($block) {
            $block->setData($data);
        }
        $this->renderLayout();
    }

    public function indibrandpendingAction() {
        $params = $this->getRequest()->getParams();
        if ($data = $this->getRequest()->getPost()) {
            $orderStatus = $params['is_new'];
            $collectionId = $params['collection_id'];
            $celebId = $params['celeb_id'];
            $action = $params['btn-submit'];
            $removedProducts = explode(",", trim($params['products_removed'], ","));

            if ($orderStatus == 0) { // open the order
                $collection = Mage::getModel('brand/assign')->getCollection()
                        ->addFieldToFilter('assign_collectionid', $collectionId)
                        ->addFieldToFilter('assign_celebid', $celebId)
                        ->addFieldToFilter('assign_by', 'c')
                        ->addFieldToFilter('assign_status', '0')
                        ->addFieldToFilter('assign_new', '0');
                $data = array("assign_new" => 1); // set to open order
                foreach ($collection as $index => $items) {
                    $items->addData($data);
                    $items->save();
                }
            } else if ($orderStatus == 1) { // ship the items in order(s)
                $collection = Mage::getModel('brand/assign')->getCollection()
                        ->addFieldToFilter('assign_collectionid', $collectionId)
                        ->addFieldToFilter('assign_celebid', $celebId)
                        ->addFieldToFilter('assign_by', 'c')
                        ->addFieldToFilter('assign_status', '0')
                        ->addFieldToFilter('assign_new', '1');
                $data = array("assign_status" => 1, "shipped_date" => date("Y-m-d")); // set to shipped
                foreach ($collection as $index => $items) {
                    if (!in_array($items['assign_productid'], $removedProducts)) {
                        $items->addData($data);
                        $items->save();
                    }
                }
                $sessionUser = Mage::helper('whibrand')->getSessionModel();

                $collection = Mage::getModel('brand/assign')->getCollection()
                        ->addFieldToFilter('assign_collectionid', $collectionId)
                        ->addFieldToFilter('assign_brandid', $sessionUser->getId())
                        ->addFieldToFilter('assign_by', 'b')
                        ->addFieldToFilter('assign_status', '0')
                        ->addFieldToFilter('assign_new', '1');
                $data = array("assign_status" => 1, "shipped_date" => date("Y-m-d")); // set brand to shipped
                foreach ($collection as $index => $items) {
                    $items->addData($data);
                    $items->save();
                }
                foreach ($removedProducts as $index => $product) {
                    $coll = Mage::getModel('brand/assign')->getCollection()
                            ->addFieldToFilter('assign_collectionid', $collectionId)
                            ->addFieldToFilter('assign_celebid', $celebId)
                            ->addFieldToFilter('assign_productid', $product)
                            ->addFieldToFilter('assign_by', 'c')
                            ->addFieldToFilter('assign_status', '0')
                            ->addFieldToFilter('assign_new', '1');
                    $coll->walk('delete');
                }
            }
        }

        $this->loadLayout();
        $block = Mage::app()->getLayout()->getBlock('inidividual_celeb_pending');
        $celeb = $block->getCelebInfo();
        $sessionUser = Mage::helper('whibrand')->getSessionModel();
        //print_r($sessionUser);

        $collection = Mage::getModel('brand/assign')->getCollection();
        $collection->getSelect()->join(array('celeb_table' => $collection->getTable('whohasitadmin/celebrity')), 'main_table.assign_celebid = celeb_table.celebrity_id', array('celeb_table.*'));
        $collection->getSelect()->join(array('user_table' => $collection->getTable('admin/user')), 'celeb_table.celebrity_user_id = user_table.user_id', array('user_table.*'));
        $collection->addFieldToFilter(array('main_table.assign_brandid'), array($sessionUser->getId()));
        $collection->addFieldToFilter(array('main_table.assign_status'), array(0));
        if ($params['sort'] == "" || $params['sort'] == "all") {
            // nothing
        } else if ($params['sort'] == "new") {
            $collection->addFieldToFilter(array('main_table.assign_new'), array(0));
        } else if ($params['sort'] == "open") {
            $collection->addFieldToFilter(array('main_table.assign_new'), array(1));
        }
        $collection->addFieldToFilter(array('main_table.assign_celebid'), array($celeb['celebinfo']['celebrity_id']));
        $collection->addFieldToFilter(array('main_table.assign_by'), array('c'));
        $collection->getSelect()->order(array('assign_collectionid asc', 'assign_new asc', 'assign_date asc'));


        $this->getLayout()->getBlock('root')->setForcedTitle($this->__('Celeb Request - Home'));

        $data['data']['new_requests'] = $collection->toArray();
        $data['data']['post'] = $params;


        if ($block) {
            $block->setData($data);
        }
        $this->renderLayout();
    }

    public function indibrandshippedAction() {
        $params = $this->getRequest()->getParams();

        $this->loadLayout();
        $block = Mage::app()->getLayout()->getBlock('inidividual_celeb_shipped');
        $celeb = $block->getCelebInfo();
        $sessionUser = Mage::helper('whibrand')->getSessionModel();
        //print_r($sessionUser);

        $collection = Mage::getModel('brand/assign')->getCollection();
        $collection->getSelect()->join(array('celeb_table' => $collection->getTable('whohasitadmin/celebrity')), 'main_table.assign_celebid = celeb_table.celebrity_id', array('celeb_table.*'));
        $collection->getSelect()->join(array('user_table' => $collection->getTable('admin/user')), 'celeb_table.celebrity_user_id = user_table.user_id', array('user_table.*'));
        $collection->addFieldToFilter(array('main_table.assign_brandid'), array($sessionUser->getId()));
        $collection->addFieldToFilter(array('main_table.assign_new'), array(1));
        $collection->addFieldToFilter(array('main_table.assign_status'), array(1));
        $collection->addFieldToFilter(array('main_table.assign_celebid'), array($celeb['celebinfo']['celebrity_id']));
        $collection->addFieldToFilter(array('main_table.assign_by'), array('c'));
        //$collection->setOrder('assign_collectionid', 'asc');
        $collection->getSelect()->order(array('assign_collectionid asc', 'assign_new asc', 'assign_date asc'));
        //echo $collection->getSelect();


        $this->getLayout()->getBlock('root')->setForcedTitle($this->__('Celeb Request - Home'));

        $data['data']['new_requests'] = $collection->toArray();
        $data['data']['post'] = $params;


        if ($block) {
            $block->setData($data);
        }
        $this->renderLayout();
    }

    public function indibrandconfirmedAction() {
        $params = $this->getRequest()->getParams();
        if ($data = $this->getRequest()->getPost()) {
            $orderStatus = $params['is_new'];
            $collectionId = $params['collection_id'];
            $celebId = $params['celeb_id'];
            $action = $params['btn-submit'];
            $removedProducts = explode(",", trim($params['products_removed'], ","));

            if ($orderStatus == 0) { // open the order
                $collection = Mage::getModel('brand/assign')->getCollection()
                        ->addFieldToFilter('assign_collectionid', $collectionId)
                        ->addFieldToFilter('assign_celebid', $celebId)
                        ->addFieldToFilter('assign_by', 'c')
                        ->addFieldToFilter('assign_status', '0')
                        ->addFieldToFilter('assign_new', '0');
                $data = array("assign_new" => 1); // set to open order
                foreach ($collection as $index => $items) {
                    $items->addData($data);
                    $items->save();
                }
            } else if ($orderStatus == 1) { // ship the items in order(s)
                $collection = Mage::getModel('brand/assign')->getCollection()
                        ->addFieldToFilter('assign_collectionid', $collectionId)
                        ->addFieldToFilter('assign_celebid', $celebId)
                        ->addFieldToFilter('assign_by', 'c')
                        ->addFieldToFilter('assign_status', '0')
                        ->addFieldToFilter('assign_new', '1');
                $data = array("assign_status" => 1); // set to shipped
                foreach ($collection as $index => $items) {
                    if (!in_array($items['assign_productid'], $removedProducts)) {
                        $items->addData($data);
                        $items->save();
                    }
                }
                foreach ($removedProducts as $index => $product) {
                    $coll = Mage::getModel('brand/assign')->getCollection()
                            ->addFieldToFilter('assign_collectionid', $collectionId)
                            ->addFieldToFilter('assign_celebid', $celebId)
                            ->addFieldToFilter('assign_productid', $product)
                            ->addFieldToFilter('assign_by', 'c')
                            ->addFieldToFilter('assign_status', '0')
                            ->addFieldToFilter('assign_new', '1');
                    $coll->walk('delete');
                }
            }
        }

        $this->loadLayout();
        $block = Mage::app()->getLayout()->getBlock('inidividual_celeb_confirmed');
        $celeb = $block->getCelebInfo();
        $sessionUser = Mage::helper('whibrand')->getSessionModel();
        //print_r($sessionUser);

        $collection = Mage::getModel('brand/assign')->getCollection();
        $collection->getSelect()->join(array('celeb_table' => $collection->getTable('whohasitadmin/celebrity')), 'main_table.assign_celebid = celeb_table.celebrity_id', array('celeb_table.*'));
        $collection->getSelect()->join(array('user_table' => $collection->getTable('admin/user')), 'celeb_table.celebrity_user_id = user_table.user_id', array('user_table.*'));
        $collection->addFieldToFilter(array('main_table.assign_brandid'), array($sessionUser->getId()));
        $collection->addFieldToFilter(array('main_table.assign_new'), array(1));
        $collection->addFieldToFilter(array('main_table.assign_status'), array(2));
        $collection->addFieldToFilter(array('main_table.assign_celebid'), array($celeb['celebinfo']['celebrity_id']));
        $collection->addFieldToFilter(array('main_table.assign_by'), array('c'));
        $collection->getSelect()->order(array('assign_collectionid asc', 'assign_new asc', 'assign_date asc'));


        $this->getLayout()->getBlock('root')->setForcedTitle($this->__('Celeb Request - Home'));

        $data['data']['new_requests'] = $collection->toArray();
        $data['data']['post'] = $params;


        if ($block) {
            $block->setData($data);
        }
        $this->renderLayout();
    }

}
