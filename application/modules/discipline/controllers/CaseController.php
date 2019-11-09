<?php
require_once 'Zend/Controller/Action.php';

class Discipline_CaseController extends Zend_Controller_Action {	
		
	public function indexAction() {
		
		$this->view->title="Case Lists";    	

		//paginator
		$oCase = new App_Model_Discipline_DbTable_Case();
		$case_list = $oCase->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($case_list));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
        
	}
	
	public function addCaseAction()
    {
    	//title
    	$this->view->title="Add Case";
    	
    	$form = new Discipline_Form_Case();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$oCase = new App_Model_Discipline_DbTable_Case();
				
				$info["case_name"]=$formData["case_name"];
				$info["case_code"]=$formData["case_code"];
				$info["description"]=$formData["description"];
				$oCase->addData($info);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'case', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
    
    public function editCaseAction(){
		//title
    	$this->view->title="Edit Case";
    	
    	$form = new Discipline_Form_Case();
    
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$oCase = new App_Model_Discipline_DbTable_Case();
				$info["case_name"]=$formData["case_name"];
				$info["case_code"]=$formData["case_code"];
				$info["description"]=$formData["description"];
				$oCase->updateData($info,$id);
				
				$this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'case', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$oCase = new App_Model_Discipline_DbTable_Case();
    			$form->populate($oCase->getData($id));
    		}
    		
    	}
    }
    
	public function deleteCaseAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$oCase = new App_Model_Discipline_DbTable_Case();
    		$oCase->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'case', 'action'=>'index'),'default',true));
    	
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
	
	
	
	
	public function formStudentCaseAction(){
		
		$this->view->title="Student Case Form";  
		
		
		if ($this->getRequest()->isPost()) {
			
			$icno = $this->getRequest()->getPost('ICNO');
			$this->view->student_icno = $icno;
			    	
			
			if($this->getRequest()->getPost('ICNO')){
				
				//get student info
				$studentDB = new App_Model_Record_DbTable_Student();
			    $student_info = $studentDB->checkStudent($icno);		    
			    
			    //if student
			    if(count($student_info>0)){			    	
			    	$this->view->student_name = $student_info["ARD_NAME"];
			    }			     
			}
				  
		    //cek no of incident
		   /* $SCaseDB = new App_Model_Discipline_DbTable_StudentCase();
		    $total_incident = $SCaseDB->getNoIncident($icno);
		    $this->view->total_incident = $total_incident;	*/
		    
		    //select case
			$oCase = new App_Model_Discipline_DbTable_Case();
			$case_list = $oCase->getData();
			$this->view->case = $case_list;
			
			
			//select penalty
			$oPenalty = new App_Model_Discipline_DbTable_Penalty();
			$penalty_list = $oPenalty->getData();
			$this->view->penalty = $penalty_list;
		    		
		}

		
	}
	
	
	
	public function addStudentCaseAction() {
		
		$auth   = Zend_Auth::getInstance();   
		
		if ($this->getRequest()->isPost()) {
		   $formdata = $this->getRequest()->getPost();
		   
		   $data["student_name"] = $formdata["student_name"];
		   $data["student_icno"] = $formdata["student_icno"];	
           $data["case_status"]  = 0; 
		   
		 /*  if (isset($formdata["start_date"]) && isset($formdata["end_date"])){   		   
		     if($formdata["start_date"]<=date("Y-m-d") && $formdata["end_date"]>=date("Y-m-d"))  $data["case_status"]=1; //set status active 	
		   }	   		   
		*/ 
		   $studentCaseDB = new App_Model_Discipline_DbTable_StudentCase();
		  
		   //cek dalam table StudentCase dah ada ke belum if belum add ;
		   $student_info = $studentCaseDB->getByICNo($data["student_icno"]);
		  
		   if($student_info["id"]){
		       $student_case_id  = $student_info["id"];
		   }else{
		   	   $student_case_id = $studentCaseDB->addData($data);
		   }
		   	   
		   
		   $info["student_case_id"] = $student_case_id;
		   $info["case_id"]      = $formdata["case_id"];
		   $info["incident_date"]= $formdata["incident_date"];
		   $info["penalty_id"]   = $formdata["penalty_id"];
		/* $info["penalty_startdt"] = $formdata["start_date"];
		   $info["penalty_enddt"]   = $formdata["end_date"];*/
		   $info["createddt"] = date("Y-m-d H:i:s");
		   $info["createdby"] = $auth->getIdentity()->id;
		   
		   $studentCaseDetailDB = new App_Model_Discipline_DbTable_StudentCaseDetail();
		   $studentCaseDetailDB->addData($info);
		   
		   $this->_redirect($this->view->url(array('module'=>'discipline','controller'=>'case', 'action'=>'student-case'),'default',true));
	
		}
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
		
?>