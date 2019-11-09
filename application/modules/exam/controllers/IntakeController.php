<?php

require_once 'Zend/Controller/Action.php';

class Exam_IntakeController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Intake Setup";
    	
    	//paginator
		$intake= new Exam_Model_DbTable_Intake();
		$intake = $grade->getPaginateGrade();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($intake));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	

}



