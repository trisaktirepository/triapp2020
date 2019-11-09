<?php

class Counseling_CasetypeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->view->defaultlanguage = $this->gobjsessionsis->UniversityLanguage;
		$sessionID = Zend_Session::getId();
		
        $this->view->title="Case Type Setup";
    }
    
    public function newcasetypeAction()
    {
        // action body
        $this->view->defaultlanguage = $this->gobjsessionsis->UniversityLanguage;
		$sessionID = Zend_Session::getId();
		
        $this->view->title="Case Type Setup";
        $auth = Zend_Auth::getInstance();
        echo $auth->getIdentity()->iduser;
        if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		echo "<pre>";
    		print_r($formData);
    		echo "</pre>";
        }
    }

}

