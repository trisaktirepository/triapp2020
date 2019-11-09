<?php

require_once 'Zend/Controller/Action.php';

class Exam_AcademicstatusController extends Zend_Controller_Action {
	
	 public function init()
    {
        /* Initialize action controller here */
    }

	
	public function indexAction() {
		$this->view->title="Academic Status";    	
    
		/*$ograde = new App_Model_Exam_DbTable_GradeGroup();					
		$group_list = $ograde->getGroupList();
        $this->view->group_list = $group_list; */
        
	}
}
?>