<?php
/*
 *  @author Alif
 *  @created Jun 14, 2011
 */

class AdmissionRecord_ChangeProgramController extends Zend_Controller_Action {
	
	public function indexAction(){
		//title
		$this->view->title = "Change Program";
		
		//search options
    	$search_matric_no = $this->_getParam('matric_no', null);
    	$this->view->search_matric_no = $search_matric_no;
    	
    	$search_ic_no = $this->_getParam('ic_no', "");
    	$this->view->search_ic_no = $search_ic_no;
    	
    	$search_id_type = $this->_getParam('id_type', 0);
    	$this->view->search_id_type = $search_id_type;
    	
    	$search_fullname = $this->_getParam('fullname', "");
    	$this->view->search_fullname = $search_fullname;
    	
		$search_program_id = $this->_getParam('program_id', 0);
    	$this->view->search_program_id = $search_program_id;
    	
    	
		//program
		$programDb = new App_Model_Record_DbTable_Program();
		$programlist = $programDb->getData();
		$this->view->programlist = $programlist;
	
    	
    	
    	//process
    	$studentDB = new App_Model_Record_DbTable_Student();
    	if ($this->getRequest()->isPost()) {
    		
    		$condition = array(
    						'matric_no'=>$search_matric_no,
    						'ic_no'=>$search_ic_no,
    						'name'=>$search_fullname,
    						'program_id'=>$search_program_id
    					);
    		
	    	//paginator
			$studentDB = new App_Model_Record_DbTable_Student();
			$students = $studentDB->getPaginateData($condition);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($students));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
	    	//paginator
			$studentDB = new App_Model_Record_DbTable_Student();
			$students = $studentDB->getPaginateData();
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($students));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
		
	}
	
	
}
?>