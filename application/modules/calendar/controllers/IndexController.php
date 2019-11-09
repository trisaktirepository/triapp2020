<?php

class Calendar_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->groups = Default_Model_Group::getGroupList();
    }


    public function eventAction()
    {
        $id = $this->_getParam('id');
        print $id;
        exit;
    }






}

