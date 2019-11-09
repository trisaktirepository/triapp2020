<?php
//ini_set('display_errors', 'on');


require_once 'Zend/Controller/Action.php';

class Exam_GradeController extends Zend_Controller_Action {
	
	/**
	 * The default action - show the home page
	 */
	
	public function indexAction() {
		$this->view->title="Grade Group Setup";    	
    
		$ograde = new App_Model_Exam_DbTable_GradeGroup();					
		$group_list = $ograde->getGroupList();
        $this->view->group_list = $group_list; 
        
        //to check university group set or not
        $condition = array('id'=>null,'group_type'=>1);
        $group_info = $ograde->get_info($condition);
        $this->view->group_info = $group_info;
        
	}
	
		
	public function addgroupAction(){
		//title
    	$this->view->title="Add Group";    	
    	    	
    	$form = new Exam_Form_Gradegroup();

    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
			//process form 
				$grade = new App_Model_Exam_DbTable_GradeGroup();
			    $grade_group_id=$grade->addgroup($formData);
			    
			    //once create group auto create grade
				$ograde = new App_Model_Exam_DbTable_Grade();	
				$ograde->setupDefaultGrade($grade_group_id);
				
				//redirect
				$this->_redirect('/exam/grade/index');		
			}else{
				$form->populate($formData);
			}        	
        }    	
     
        $this->view->form = $form;
	}
	
	public function editgroupAction(){
		//title
    	$this->view->title="Edit Group";
    	
    	$form = new Exam_Form_Gradegroup();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {				
	    			
				$grade = new App_Model_Exam_DbTable_GradeGroup(); 
				$grade->updategroup($formData,$id);
				
				$this->_redirect('/exam/grade/index');
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			
    			$grade = new App_Model_Exam_DbTable_GradeGroup();
    			$form->populate($grade->getData($id));
    			
    		}
    		
    	}
	}
	
	public function deleteAction(){
		$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		
    		$oGroup= new App_Model_Exam_DbTable_GradeGroup();
    		$oGroup->deletedata($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'grade', 'action'=>'index'),'default',true));
    	
	}
	
	
	public function activateAction(){
		
		$id = $this->_getParam('id', 0);
		
		$grade = new App_Model_Exam_DbTable_GradeGroup(); 
		
		$data = array("group_type" => 1);
		
		$grade->updategrouptype($data,$id);//to set as a university grade.
		
		//redirect
		$this->_redirect('/exam/grade/');		
	}
	
	public function deactivateAction(){
		
		$id = $this->_getParam('id', 0);
		
		$grade = new App_Model_Exam_DbTable_GradeGroup(); 
		
		$data = array("group_type" => '');
		
		$grade->updategrouptype($data,$id);//to set as a university grade.
		
		//redirect
		$this->_redirect('/exam/grade/');		
	}
	
	public function indexgradeAction(){
		//title
    	$this->view->title="Grade Setup"; 

    	$id = $this->_getParam('id', 0);
    	$this->view->grade_group_id=$id;
    	
    	
    	//get group info
    	$oGroup= new App_Model_Exam_DbTable_GradeGroup();
    	$condition=array("id"=>$id,"group_type"=>null);    	
    	$g_info=$oGroup->get_info($condition);
    	
    	$this->view->group_name=$g_info["group_name"];
    	$this->view->verification_status=$g_info["gv_status"];
        //$this->view->grade_verification_id=$g_info["grade_verification_id"];
    	
    	$condition = array("grade_group_id"=>$id);
    
		$ograde = new App_Model_Exam_DbTable_Grade();					
		$grade_list = $ograde->search($condition);
        $this->view->grade_list = $grade_list; 
        
        
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		$msg = $this->_flashMessenger->getMessages();
		
		if($msg!=null){
		
		    $this->view->noticeMessage = $msg[0];
		
		}
        
	}
	
	public function adduAction()
    {
    	//title
    	$this->view->title="Add New Grade";  

    	$groupID = $this->_getParam('id', 0);
    	$this->view->groupID=$groupID;
    	    	
    	$form = new Exam_Form_Grade(array('groupID'=>$groupID));
    	  	    	

    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {			
		
				$grade_group_id = $formData["grade_group_id"];
				
			    //process form 
				$grade = new App_Model_Exam_DbTable_Grade();
			    $grade->addGrade($formData);
				
				//redirect
				$this->_redirect('/exam/grade/indexgrade/id/'.$grade_group_id);		
			}else{
				$form->populate($formData);
			}        	
        }    	
     
        $this->view->form = $form;
        
        
    }
    
	public function edituAction(){
    	//title
    	$this->view->title="Edit Grade";
    	
    	$id = $this->_getParam('id', 0);
    	
    	$groupID = $this->_getParam('grade_group_id', 0);
    	$this->view->groupID=$groupID;
    	
    	$form = new Exam_Form_Grade(array('groupID'=>$groupID));    	    	
    	$this->view->form = $form; 	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$grade = new App_Model_Exam_DbTable_Grade(); 
				$grade->updateGrade($formData,$id);
				
				$this->_redirect('/exam/grade/indexgrade/id/'.$groupID);
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			
    			$grade = new App_Model_Exam_DbTable_Grade();
    			$form->populate($grade->getGrade($id));
    			
    		}
    		
    	}
    }
    
    
    public function editgradeAction(){
    	
    	//title
    	$this->view->title="Edit Grade";
    	
    	    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			$iteration  = $formData["iteration"];	
			$groupID    = $formData["grade_group_id"];			
		   	
		   	 for($i=1; $i<=$iteration; $i++){
		   	 	
		   	 	$id                = $formData["id".$i];
		   	 	$data["symbol"]    = $formData["symbol".$i];
		   	 	$data["point"]     = $formData["point".$i];
		   	 	$data["status"]    = $formData["status".$i];
		   	 	$data["min_mark"]  = $formData["min_mark".$i];
		   	 	$data["max_mark"]  = $formData["max_mark".$i];
		   	 	$data["level"]     = $formData["level".$i];
		   	 	
		   	 	$gradeDB = new App_Model_Exam_DbTable_Grade();
		   	 	
		   	 	if($id){ //update
		   	 				   	 		
		   	 		$gradeDB->updateGrade($data,$id);
		   	 		
		   	 	}else{ //add
		   	 		
		   	 		$data["grade_group_id"] = $formData["grade_group_id"];	
		   	 		$gradeDB->addGrade($data);
		   	 	}
		   	 	
		   	 }
		   	 //exit;
		   	 $this->_redirect('/exam/grade/editgrade/grade_group_id/'.$groupID);
			
			
    	}else{
    		
    		$groupID = $this->_getParam('grade_group_id', 0);
    	    $this->view->groupID=$groupID;
    	    	
    	
    		//get grade
	    	$gradeDB = new App_Model_Exam_DbTable_Grade();
	    	$condition = array("grade_group_id" => $groupID);
	    	$rs_grade = $gradeDB->search($condition);
	    	$this->view->rs_grade = $rs_grade;
    		
    	}
    	
    	
    }
    
    
	public function deletegradeAction($id = null){
    	
		$id = $this->_getParam('id', 0); 
    	$grade_group_id = $this->_getParam('grade_group_id', 0); 
    	    	
    	
    	 if ($this->_request->isPost()) {   
    	 
    	 	 $id                = $this->getRequest()->getPost('id');	
    	 	 $grade_group_id    = $this->getRequest()->getPost('grade_group_id');		
    	 	 
    	 	 for($i=0; $i<count($id); $i++){
			 	
				$gradeDB = new App_Model_Exam_DbTable_Grade();
    			$gradeDB->deleteGrade($id[$i]);
    		 }
    	 }
    		
    	$this->_redirect('/exam/grade/editgrade/grade_group_id/'.$grade_group_id);
    	
    }
    
    
    /*
    	Function : To print verification grade form to get signature from authorised person    
    */
    
	public function requestAction(){
    	$this->_helper->layout->setLayout('result');
    	
    	$id = $this->_getParam('id', 0); //group id
    	$this->view->grade_group_id=$id;    	
    	
    	$program_id  = $this->_getParam('program_id', 0);
    	$semester_id = $this->_getParam('semester_id', 0);  
    	
    	
    	//get fullname session id  
    	$auth   = Zend_Auth::getInstance();   	
    	$userDB = new SystemSetup_Model_DbTable_User();
    	$user   = $userDB->getData($auth->getIdentity()->id);
    	$this->view->username=$user["fullname"];
    	
    	
    	
    	/*  -----------------------------------------------------------
    		To get Random Code for verification request No.
    		Each time Random Code generated. Save kan dalam database.
    	    ------------------------------------------------------------ */
    	
    	$request_number= 'UGV'.rand(111111,999999);
    	$this->view->request_number =$request_number;
    	
    	//TODO: Save dalam database if already exist update sahaja.
    	$gradeVerifyDB = new App_Model_Exam_DbTable_GradeVerification();
    	
    	$info = $gradeVerifyDB->getData($id);    	
    	$grade_verification_id = $info["id"];    	
    	
    	$data = array('group_id'   => $id,
					  'request_no' => $request_number,
					  'createddt'  => date("Y-m-d H:i:s"),
					  'createdby'  => $auth->getIdentity()->id);
    	
    	if($grade_verification_id) {    	
    		$gradeVerifyDB->updateData($data,$grade_verification_id);    	
    	}else{
    		$gradeVerifyDB->insertData($data);  
    	}
    	
    	
    	
    	
    	if($program_id && $semester_id){
    		 $ograde = new App_Model_Exam_DbTable_CustomGrade();					
			 $grade_list = $ograde->getProgramGrade($program_id,$semester_id);
         	 $this->view->grade_list = $grade_list;
    	}else{	    	
	        $condition = array("grade_group_id"=>$id);    
			$ograde = new App_Model_Exam_DbTable_Grade();					
			$grade_list = $ograde->search($condition);
	        $this->view->grade_list = $grade_list;  
    	}
    }
    
    
	public function verifyAction(){
    	
    	   $this->_helper->layout->setLayout('result');
    	   
    	   $request_no     = $this->_getParam('request_no', 0);
    	   $grade_group_id = $this->_getParam('id', 0); //group id
    	   $this->view->grade_group_id=$grade_group_id;
    	   
    	   
    	   
    	   if($request_no){  
    	   	
    	   		$auth = Zend_Auth::getInstance(); 
    	   	    $oVerifyGrade = new App_Model_Exam_DbTable_GradeVerification();
    	   	    
    	   	     //check request_no ada x dalam sistem
    	   	    $condition = array('group_id'=>$grade_group_id,'request_no'=>$request_no);
    	       	$rs_info   = $oVerifyGrade->getInfo($condition);
    	     
    	       	
    	       	if($rs_info){	       		
		       		
    	       		$data = array('status'   =>  1, 
							      'verifydt' =>  date("Y-m-d H:i:s"),
							      'verifyby' =>  $auth->getIdentity()->id);
						   
						 
					$oVerifyGrade->updateData($data,$rs_info["id"]);
					
					//update table e011_grade_group
					$gradeGroupDB = new App_Model_Exam_DbTable_GradeGroup();
					$data=array("grade_verification_id"=>$grade_verification_id);
					$gradeGroupDB->updateData($data,$grade_group_id);		
					 
					$this->_helper->flashMessenger->addMessage("Grade has been successfully verified.");
    	       		$this->_redirect('/exam/grade/indexgrade/id/'.$grade_group_id);		
    	        
    	       	}else{
    	       		
    	       		$this->_helper->flashMessenger->addMessage("Request No did not match.Please try again.");
    	       		$this->_redirect('/exam/grade/indexgrade/id/'.$grade_group_id);		
    	       	}
				
				
				
    	   }  

    
    }
    
    
    
    //assign grade to program/semester/course
    public function viewAction() {
		
		
		//title
    	$this->view->title="Grade Exception";  	
    	
         	
         	$program_id  = $this->_getParam('program_id', 0); 
        	$this->view->program_id=$program_id;

        	$semester_id = $this->_getParam('semester_id', 0); 
    		$this->view->semester_id=$semester_id;
                      	
        
         	    //get semester
                 $semester_id=0;
		         if ($this->_request->isPost()) {         	
					 $semester_id= $this->getRequest()->getPost('semester_id');
					 $this->view->semester_id = $semester_id;		
		         }
		         
		    	$oSemester = new App_Model_Record_DbTable_Semester();
		    	$semester_list = $oSemester->getData();
		    	$this->view->semester = $semester_list;  
		    	
		   	
       			 $program_id=0;
		         if ($this->_request->isPost()) {         	
					 $program_id= $this->getRequest()->getPost('program_id');
					 $this->view->program_id = $program_id;
		         }
		        
		         if($semester_id){
			        //get Program
			    	$program = new App_Model_Record_DbTable_Program();
			        $program_list = $program->selectSemProgram($semester_id);
			    	$this->view->program = $program_list; 
			    	
			    	
		         }		      
		         
		        if($semester_id && $program_id){
			        $ograde = new App_Model_Exam_DbTable_CustomGrade();					
					$group_info = $ograde->getGroupInfo($program_id,$semester_id);
					
					//by default if there were no group assigned follow university grade
					$group_id = $group_info["grade_group_id"];
					
					
					
					if(!$group_id){
						//check university group id
						//to check university group set or not
						$gradeDB = new App_Model_Exam_DbTable_GradeGroup();	
				        $condition = array('group_type'=>1);
				        $group_info = $gradeDB->get_info($condition);
				        $this->view->group_info = $group_info;
				        $group_id = $group_info["id"];
				        $this->view->group_name = $group_info["group_name"];
					}
	         		
	         		$oUser = new SystemSetup_Model_DbTable_User();
	    	 		$user = $oUser->getData($group_info["modifiedby"]); 
	    	        $fullname = $user["fullname"];
	    	        
	    	          
			        $this->view->group_id=$group_id;
	         		$this->view->modifyby=$fullname;
	         		$this->view->modifydt=$group_info["modifieddt"];
	         		$this->view->group_name =$group_info["group_name"];
	         		
	         		         		
	         		//search on verified grade/group
			         $oGrade = new App_Model_Exam_DbTable_GradeGroup();
			         $list_group = $oGrade->search();
			         $this->view->group = $list_group;
			       
		         
	         		
	         		if($group_id){
		         		$condition = array("grade_group_id"=>$group_id);
		         		
		         		$oGrade = new App_Model_Exam_DbTable_Grade();
	    				$grade_list = $oGrade->search($condition);
	    				$this->view->grade_list = $grade_list; 
	         		}
		        }
         	
	}
	
	public function changeAction(){
		//title
    	$this->view->title="Change Grade";
    	  	
		     	
    			$group_id = $this->_getParam('group_id', 0); 
    			$this->view->group_id=$group_id;
    			
    			//check pernah buat change request x
    			$oObject = new App_Model_Exam_DbTable_CustomGrade();
    			$grade_info = $oObject->getData($group_id);
    			
    			$semester_id = $grade_info["semester_id"];
    			$program_id  = $grade_info["program_id"];
    			$custom_grade_id  = $grade_info["id"];
    			
    			$this->view->semester_id = $grade_info["semester_id"];
    			$this->view->program_id = $grade_info["program_id"];
    			$this->view->custom_grade_id = $grade_info["id"];
    			
    			//get program name
	    		$oProgram = new App_Model_Record_DbTable_Program();
	    		$program_info = $oProgram->getData($program_id);
	    		$this->view->program_name = $program_info["main_name"];
	    	
	    		//get semester name    		
	    	    $oSemester = new App_Model_Record_DbTable_Semester();
	    	    $semester_info = $oSemester->getData($semester_id);    	   
	    	    $this->view->semester_name = $semester_info["name"];

		         //search on verified grade/group
		         $oGrade = new App_Model_Exam_DbTable_GradeGroup();
		         $list_group = $oGrade->search();
		         $this->view->group = $list_group;
		         
		         
		        if($semester_id && $program_id){
			        $ograde = new App_Model_Exam_DbTable_CustomGrade();					
					$grade_list = $ograde->getGradeInfo($program_id,$semester_id);
	         		$this->view->grade_list = $grade_list;
		        }
        
	}
	
	
	public function changerequestAction(){
    	$this->_helper->layout->setLayout('result');
    	$auth = Zend_Auth::getInstance(); 
    	
    	$oUser = new SystemSetup_Model_DbTable_User();
	    $user = $oUser->getData($auth->getIdentity()->id); 
	    $this->view->username=$user["fullname"];    	
    	
    	
    	$program_id  = $this->_getParam('program_id', 0);
    	$semester_id = $this->_getParam('semester_id', 0);  
    	$group_id    = $this->_getParam('group_id', 0);

    		//get program name
    		$oProgram = new App_Model_Record_DbTable_Program();
    		$program_info = $oProgram->getData($program_id);
    		$this->view->program_name = $program_info["main_name"];
    	
    		//get semester name    		
    	    $oSemester = new App_Model_Record_DbTable_Semester();
    	    $semester_info = $oSemester->getData($semester_id);    	   
    	    $this->view->semester_name = $semester_info["name"];
    	    
    	    //get group name
    	     $oGrade = new App_Model_Exam_DbTable_GradeGroup();
		     $group_info = $oGrade->getData($group_id);
		     $this->view->group_name = $group_info["group_name"];
    	
    	
    	$request_number= 'GRF'.uniqid(rand(111111,999999));
    	$this->view->request_number =$request_number;
    	
    	$condition = array("grade_group_id"=>$group_id);
    	
		$ograde = new App_Model_Exam_DbTable_Grade();					
		$grade_list = $ograde->search($condition);
        $this->view->grade_list = $grade_list; 
    	
    }
    
    
    public function confirmAction(){
    	 $this->_helper->layout->setLayout('result');
    	 
    	 
    	  $semester_id    = $this->_getParam('semester_id', 0);
    	  $program_id     = $this->_getParam('program_id', 0);
    	  $group_id       = $this->_getParam('group_id', 0);
    	  $request_no     = $this->_getParam('request_no', 0);
    	   
    	   $this->view->semester_id = $semester_id;
    	   $this->view->program_id  = $program_id;
    	   $this->view->group_id    = $group_id;
    	   $this->view->request_no  = $request_no;

    	 
    	   if($request_no){  
    	   	
    	   	    $oVerifyGrade = new App_Model_Exam_DbTable_GradeVerification();
				$verification_id = $oVerifyGrade->verifyGrade($request_no);
    	   	    
    	   	    $data["program_id"]       = $program_id;
    	   	    $data["semester_id"]      = $semester_id;
    	   	    $data["grade_group_id"]   = $group_id;
    	   	    $data["verification_id"]  = $verification_id;
    	   	    
    	   	    $CustomGradeDB = new App_Model_Exam_DbTable_CustomGrade();	
    	   	    $CustomGradeDB->insertData($data);
    	   	   
				EXIT;
				$this->_helper->flashMessenger->addMessage("Grade has been successfully verified.");
				$this->_redirect('/exam/grade/view/');
    	   }    
    	  
    	   
    }
    
    
    
    public function gradeExceptionAction(){
    	
    	 $this->view->title="Grade Exception";
    	
    	 // To list available group
    	 $ograde = new App_Model_Exam_DbTable_GradeGroup();					
		 $group_list = $ograde->getGroupList();
         $this->view->group_list = $group_list; 
         
        //get semester
        $oSemester = new App_Model_Record_DbTable_Semester();
    	$semester_list = $oSemester->getData();
    	$this->view->semester = $semester_list;  
    	
    	//get Program
    	$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getData();
    	$this->view->program = $program_list; 
    }
}
		
?>