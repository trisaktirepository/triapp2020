<?php

class Company_RecordController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

	public function profileAction()
    {
        $this->view->title = "Candidate Profile";
        
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
		
		$auth = Zend_Auth::getInstance();
		$company_id = $auth->getIdentity()->id;
        
    	$studentDB = new App_Model_Record_DbTable_Student();
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$condition = array(
    						'matric_no'=>$search_matric_no,
    						'ic_no'=>$search_ic_no,
    						'fullname'=>$search_fullname,
    						'program_id'=>$search_program_id
    					);
    		
	    	//paginator
			$studentDB = new App_Model_Record_DbTable_Student();
			$students = $studentDB->getPaginateCompanyStudentData($company_id, $condition);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($students));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
			
    	}else{
    					
	    	//paginator
			$studentDB = new App_Model_Record_DbTable_Student();
			$students = $studentDB->getPaginateCompanyStudentData($company_id);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($students));
			$paginator->setItemCountPerPage(15);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
			$this->view->paginator = $paginator;
    	}
        
    }
    
    public function viewAction(){
    	$this->view->title = "Profile Detail";
    	
    	$student_id = $this->_getParam('id', 0);
    	
    	$studentDB = new App_Model_Record_DbTable_Student();
    	$this->view->student = $studentDB->getStudent($student_id);
    }
    
    public function addProfileStep1Action(){
    	$this->view->title = "Add New Candidate Profile - Personal Identity";
    	
    	$id= $this->_getParam('id', 0);
		$this->view->id = $id;
    	
		if ($id) {
			
			$IndexDbTable = new App_Model_Record_DbTable_Student();
			$data_applicant = $IndexDbTable->getApplicant($id);
			$this->view->applicant = $data_applicant;
			
		}
    }
    
	public function addProfileStep2Action()
    {
        // title
        $this->view->title = "Add New Candidate Profile - Personal Particular";
    	
    	$id= $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$lookupDB = new App_Model_General_DbTable_Lookup();
		$religionData = $lookupDB->getData(8);
		$this->view->religion = $religionData;
		
		$raceData = $lookupDB->getData(13);
		$this->view->race = $raceData;
		
		$qualificationData = $lookupDB->getData(14);
		$this->view->qualification = $qualificationData;
    	
		$takafulDB = new App_Model_General_DbTable_TakafulOperator();
		$takafulData = $takafulDB->getDataType(2);
		$this->view->takaful = $takafulData;
		
		$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia only
		$this->view->state = $stateData;
		
		$studentDbTable = new App_Model_Record_DbTable_Student();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			if($formData["qic1"]){
				$icno = $formData["qic1"].$formData["qic2"].$formData["qic3"];
				$this->view->icno = $icno;
				
				$checkStudent = $studentDbTable->checkStudent($icno);
				
				if($checkStudent){
					
					//already registered
					$this->view->student = $checkStudent;
					
					//redirect
					$this->_redirect($this->view->url(array('module'=>'company','controller'=>'record', 'action'=>'add-profile-step3', 'ic'=>$icno),'default',true));
					
					
				}else{
					
				}
			}
		}else{
			
			//redirect
			//$this->_redirect($this->view->url(array('module'=>'company','controller'=>'record', 'action'=>'add-profile-step1'),'default',true));
		}
    }
    
    public function addProfileStep3Action(){
    	
    	$this->view->title = "Add New Candidate Profile - Confirmation";
    	
    	$studentDbTable = new App_Model_Record_DbTable_Student();
    	
    	if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$date = date('Y-m-d H:i:s');
			
			$icno = $formData["ARD_IC"];
			$this->view->icno = $icno;
			
			$checkStudent = $studentDbTable->checkStudent($icno);
			$auth = Zend_Auth::getInstance();
				
			if(!$checkStudent){
			
				$data = array (
						"ARD_NAME" => strtoupper($formData["ARD_NAME"]),
					    "ARD_EMAIL" => $formData["ARD_EMAIL"],
						"ARD_DOB" => $formData["ARD_DOB"],
						"ARD_IC" => $formData["ARD_IC"],
					    "ARD_ADDRESS1" => $formData["ARD_ADDRESS1"],
					    "ARD_ADDRESS2" => $formData["ARD_ADDRESS2"],
					    "ARD_POSTCODE" => $formData["ARD_POSTCODE"],
					    "ARD_STATE" => $formData["ARD_STATE"],
						"ARD_CITY" => $formData["ARD_CITY"],
					    "ARD_COUNTRY" => $formData["ARD_COUNTRY"],
					    "ARD_PHONE" => $formData["ARD_PHONE"],
					    "ARD_HPHONE" => $formData["ARD_HPHONE"],
					    "ARD_RACE" => $formData["ARD_RACE"],
					    "ARD_RELIGION" => $formData["ARD_RELIGION"],
					    "ARD_SEX" => $formData["ARD_SEX"],
					    "ARD_QUALIFICATION" => $formData["ARD_QUALIFICATION"],
					    "ARD_CLIENTTYPE" => 1,
						"username" => $formData["username"],
						"password" => md5($formData["clearpass"]),
						"clearpass" => $formData["clearpass"],
						"ARD_DATE_APP" => $date
				);
				
				//tag to company
				$data['ARD_COMPANY_ID'] = $auth->getIdentity()->id;
				
				//takaful operator
				if($auth->getIdentity()->idClienttype ==2){
						$data['ARD_TAKAFUL'] = $auth->getIdentity()->id;
				}
							
				//TODO:check valid username
				
				
				$add = $studentDbTable->addStudent($data);
				$this->view->idApp = $add;
				
				if($add!=null || $add!=0){
					$this->view->noticeSuccess = "Success. New candidate profile added";
				}else{
					$this->view->noticeError = "Error adding new candidate profile.";
				}
				
				$this->view->student = $data;
				
			}else{
				
				$data = $studentDbTable->checkStudent($icno);
				
				$this->view->student = $data;
				$this->view->noticeError = "Error. Candidate already have profile in the system. If this candidate is not in your candidate list, please contact IBFIM to tag this student with your company";
			}
    	}else{
    		$icno= $this->_getParam('ic', 0);
    		
    		if($icno!=0){
				
				$data = $studentDbTable->checkStudent($icno);
				
				$this->view->student = $data;
				$this->view->noticeError = "Error. Candidate already have profile in the system. If this candidate is not in your candidate list, please contact IBFIM to tag this student with your company";
			}else{
	    		//redirect
				$this->_redirect($this->view->url(array('module'=>'company','controller'=>'record', 'action'=>'add-profile-step1'),'default',true));
			}
    	}
    }
    
	public function editProfileAction()
    {
        $this->view->title = "Edit Candidate Profile";
        
        $id= $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$lookupDB = new App_Model_General_DbTable_Lookup();
		$religionData = $lookupDB->getData(8);
		$this->view->religion = $religionData;
		
		$raceData = $lookupDB->getData(13);
		$this->view->race = $raceData;
		
		$qualificationData = $lookupDB->getData(14);
		$this->view->qualification = $qualificationData;
    	
		$takafulDB = new App_Model_General_DbTable_TakafulOperator();
		$takafulData = $takafulDB->getDataType(2);
		$this->view->takaful = $takafulData;
		
		$stateDB = new App_Model_General_DbTable_State();
		$stateData = $stateDB->getState(129); //state in malaysia only
		$this->view->state = $stateData;
		
		$studentDbTable = new App_Model_Record_DbTable_Student();
		
    	if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$data = array (
					"ARD_NAME" => strtoupper($formData["ARD_NAME"]),
				    "ARD_EMAIL" => $formData["ARD_EMAIL"],
				    "ARD_ADDRESS1" => $formData["ARD_ADDRESS1"],
				    "ARD_ADDRESS2" => $formData["ARD_ADDRESS2"],
				    "ARD_POSTCODE" => $formData["ARD_POSTCODE"],
				    "ARD_STATE" => $formData["ARD_STATE"],
					"ARD_CITY" => $formData["ARD_CITY"],
				    "ARD_COUNTRY" => $formData["ARD_COUNTRY"],
				    "ARD_PHONE" => $formData["ARD_PHONE"],
				    "ARD_HPHONE" => $formData["ARD_HPHONE"],
				    "ARD_RACE" => $formData["ARD_RACE"],
				    "ARD_RELIGION" => $formData["ARD_RELIGION"],
				    "ARD_SEX" => $formData["ARD_SEX"],
				    "ARD_TAKAFUL" => $formData["ARD_TAKAFUL"],
				    "ARD_QUALIFICATION" => $formData["ARD_QUALIFICATION"]
			);
						
			$studentDbTable->updateStudent($data,$id);
				
			//redirect
			$this->_redirect($this->view->url(array('module'=>'company','controller'=>'record', 'action'=>'view', 'id'=>$id),'default',true));
				
		}else{
			$getStudent = $studentDbTable->getStudent($id);
			$this->view->student = $getStudent;
			$this->view->icno = $getStudent["ARD_IC"];
		}
    }

	public function progressAction()
    {
        $this->view->title = "Candidate Progress";
    }
}

