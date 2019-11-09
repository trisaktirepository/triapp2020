<?php

class Company_DiscplineController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->title = "Discpline List";
    }
    
	public function studentCaseAction(){
		
		$this->view->title="Student Case Lists";  
		
		$studentCaseDB = new App_Model_Discipline_DbTable_StudentCase();
	
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$condition = array('keyword'=>$formData["keyword"]);
			
			$student_data = $studentCaseDB->search($condition);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentCaseDB->getPaginateData();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		

	}
	
	public function viewStudentCaseAction() {
	
		$this->view->title="View Student Case"; 
		 
		$id = $this->_getParam('id', 0);
		
		$studentCaseDB = new App_Model_Discipline_DbTable_StudentCase();
		$student_case  = $studentCaseDB->getStudentCaseDetail($id);
		$this->view->student_case = $student_case;
		
		$this->view->student_name= $student_case["0"]["student_name"];
		$this->view->student_icno= $student_case["0"]["student_icno"];
		$this->view->case_status= $student_case["0"]["case_status"];
		
		if ($this->getRequest()->isPost()) {
		   $info["case_status"] = $this->getRequest()->getPost('release_status');
		   $studentCaseDB->updateData($info,$id);
		   $this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'case', 'action'=>'student-case'),'default',true));
		}
		
       
	}
}

