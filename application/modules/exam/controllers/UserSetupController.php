<?php
class Exam_UserSetupController extends Zend_Controller_Action {

	/* Initialize action controller here */
    public function init()
    {
        
    }
    
    
    /*
     * User Setup
     */
    public function userAction(){
    	$this->view->title="Examiner User Access";
    	
    	$examinerDB = new App_Model_Exam_DbTable_Examiner();
    	
    	//paginator
		$users = $examinerDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($users));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		
		//flash msg
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
    }
    
    public function addUserAction(){
    	$this->view->title="Add Examiner User Access";
    	
    	$form = new Exam_Form_Examiner();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$examinerDB = new App_Model_Exam_DbTable_Examiner();
				
				$auth = Zend_Auth::getInstance();
				
				try{
					$data = array(
								'name'=>$formData['name'],
								'username'=>$formData['email'],
								'password'=>md5($formData['email']),
								'email'=>$formData['email'],
								'branch_id'  => $formData['branch_id'],
								'create_by'=>$auth->getIdentity()->id
							);
					
					$uid = $examinerDB->addData($data);
					
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While add new user");
				}
				
				
				if($uid!=null){
					$this->_helper->flashMessenger->addMessage("Successfuly add new user");
				}
				
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'user'),'default',true));
					
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
    public function editUserAction(){
    	$this->view->title="Edit Examiner User Access";
    	
    	$uid = $this->_getParam('id', 0);
    	$this->view->uid = $uid;
    	
    	$form = new Exam_Form_Examiner();
    	
    	$examinerDB = new App_Model_Exam_DbTable_Examiner();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$data = array(
							'name'=>$formData['name'],
							'email'=>$formData['email'],
							'branch_id'=>$formData['branch_id']
						);
						
				$examinerDB->updateData($formData,$uid);
				$this->_helper->flashMessenger->addMessage("Successfuly Update the user");
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'user'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }else{
    		if($uid!=0){
    			$form->populate($examinerDB->getData($uid));
    		}
    	}
    	
        $this->view->form = $form;
    }
    
    public function deleteUserAction(){
    	$uid = $this->_getParam('id', 0);
    	$this->view->uid = $uid;
    	
    	if($uid!=0){
    		$examinerDB = new App_Model_Exam_DbTable_Examiner();
    		$examinerDB->deleteData($uid);
    		$this->_helper->flashMessenger->addMessage("Successful deleting user");
    	}else{
    		$this->_helper->flashMessenger->addMessage("Error occurs while deleting user. <br />User not deleted");
    	}
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'user'),'default',true));
    }
    
    
    /*
     * Tagging Setup
     */
    public function taggingAction(){
    	$this->view->title="Examiner Tagging";
    	
		//flash msg
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
    }
    
    public function taggingExaminerAction(){
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	$examinerDB = new App_Model_Exam_DbTable_Examiner();
    	
    	//paginator
		$users = $examinerDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($users));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    }
    
    public function taggingSemesterAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        //paginator
        $semesterDB = new App_Model_Record_DbTable_Semester();
        
        $semester = $semesterDB->getPaginateData();
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($semester));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
        
    }
    
    public function taggingDetailExaminerAction(){
    	$this->view->title="Examiner User Tagging Detail";
    	
    	$uid = $this->_getParam('uid', 0);
    	$this->view->uid = $uid;
    	
    	$semester_id = $this->_getParam('semester_id', 0);
    	$this->view->semester_id = $semester_id;
    	
    	if($uid==0){
    		//redirect if no id given
			$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'tagging'),'default',true));
    	}
    	
    	//examiner data
    	$examinerDB = new App_Model_Exam_DbTable_Examiner();
    	$examiner = $examinerDB->getData($uid);
    	$this->view->examiner = $examiner;
    	
    	//semester data
    	$semesterDB = new App_Model_Record_DbTable_Semester();
    	$this->view->semester_list = $semesterDB->getData();
    	
    	if($semester_id!=0){
    		$examiner_taggingDB = new Exam_Model_DbTable_ExaminerTagging();
    		$tagging_data = $examiner_taggingDB->getExaminerData($uid,$semester_id);
    		
    		$this->view->data = $tagging_data;
    	}
    	
    	//flash msg
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
    	
    }
    
    public function taggingDetailExaminerAddAction(){
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	$this->view->title="Examiner User Tagging Detail";
    	
    	$uid = $this->_getParam('uid', 0);
    	$this->view->uid = $uid;
    	
    	$semester_id = $this->_getParam('semester_id', 0);
    	$this->view->semester_id = $semester_id;
    	
    	$form = new Exam_Form_ExaminerTagging(array('semesterID'=>$semester_id, 'uid'=>$uid));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form
				$auth = Zend_Auth::getInstance();
				
				$formData['last_edit_by'] = $auth->getIdentity()->id;
				$formData['last_edit_date'] =date("Y-m-d H:i:s");
				
				$examiner_taggingDB = new Exam_Model_DbTable_ExaminerTagging();
				$examiner_taggingDB->addData($formData);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'tagging-detail-examiner', 'uid'=>$uid, 'semester_id'=>$semester_id),'default',true));
					
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function taggingDetailExaminerEditAction(){
    	
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
    	$this->view->title="Examiner User Tagging Detail";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$examiner_taggingDB = new Exam_Model_DbTable_ExaminerTagging();
        $data = $examiner_taggingDB->getData($id);
        $this->view->component_id = $data['component_id'];
    	
    	$form = new Exam_Form_ExaminerTaggingEdit(array('semesterID'=>$data['semester_id'], 'uid'=>$data['examiner_id']));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form
				$auth = Zend_Auth::getInstance();
				
				$data_save = array(
						'course_id'	=> 	$formData['course_id'],
						'component_id' => $formData['component_id'],
						'last_edit_by' => $auth->getIdentity()->id,
						'last_edit_date' => date("Y-m-d H:i:s")	
						);
				
				$examiner_taggingDB = new Exam_Model_DbTable_ExaminerTagging();
				$examiner_taggingDB->updateData($data_save, $id);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'tagging-detail-examiner', 'uid'=>$data['examiner_id'], 'semester_id'=>$data['semester_id']),'default',true));
					
			}else{
				$form->populate($formData);
			}
        	
        }else{
        	$form->populate($data);
        }
    	
        $this->view->form = $form;
    }
    
    public function taggingDetailExaminerDeleteAction(){
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	
        
    	if($id!=0){
    		$examiner_taggingDB = new Exam_Model_DbTable_ExaminerTagging();
        	$data = $examiner_taggingDB->getData($id);
    		
        	$examiner_taggingDB->deleteData($id);
        	
    		$this->_helper->flashMessenger->addMessage("Successful deleting user");
    	}else{
    		$this->_helper->flashMessenger->addMessage("Error occurs while deleting user. <br />User not deleted");
    	}
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'user-setup', 'action'=>'tagging-detail-examiner', 'uid'=>$data['examiner_id'], 'semester_id'=>$data['semester_id']),'default',true));
    }
    
    public function ajaxGetComponentAction(){
    	
    	$course_id = $this->_getParam('cid', 0);
    	$this->view->cid = $course_id;
    	
    	$AsscomponentDB = new App_Model_Exam_DbTable_Asscomponent();
    	$rs_component = $AsscomponentDB->getAsscomponent(false,$course_id);
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        $ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($rs_component);
		
		$this->view->json = $json;
    }
    
    
    
    
	public function taggingDetailSemesterAction(){
    	$this->view->title="Examiner User Tagging Detail";
    	
    	$semester_id = $this->_getParam('sem_id', 0);
    	$this->view->semester_id = $semester_id;
    	
    	
    }
}
?>