<?php
require_once 'Zend/Controller/Action.php';

class Setup_AsscomponentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        //title
    	$this->view->title="Assesment Component Setup";
    	
    	//get masterProgram
    	$masterprogram = new Setup_Model_DbTable_MasterProgram();
        $masterprogram_list = $masterprogram->selectMasterProgram();
    	$this->view->masterprogram = $masterprogram_list;    	
    	
    	//get intake
    	$intake = new Setup_Model_DbTable_Intake();
        $intake_list = $intake->selectIntake();
        $this->view->intakes = $intake_list;

        $masterProgramID=0;
         if ($this->_request->isPost()) {         	
			 $masterProgramID = $this->getRequest()->getPost('masterProgramID');
			 $this->view->masterProgramID = $masterProgramID;
         }

         $intake=0;
         if ($this->_request->isPost()) {   
			 $intake_id = $this->getRequest()->getPost('intake_id');
			 $this->view->intake_id = $intake_id;	
         }  
    }
    
    
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Component";
    	
    	$form = new Setup_Form_Asscomponent();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$grade = new Setup_Model_DbTable_Asscomponent();
				$grade->addGrade($formData);
				
				//redirect
				$this->_redirect('/setup/asscomponent/');		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
       
        
    }


}

