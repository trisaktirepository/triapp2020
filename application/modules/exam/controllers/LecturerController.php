<?php
class Exam_LecturerController extends Zend_Controller_Action {

	/* Initialize action controller here */
    public function init()
    {
        
    }
    

    public function indexAction(){
    	$this->view->title="Lecturer Profile";
    	
    	$lecturerDB = new App_Model_Exam_DbTable_Lecturer();
    	
    	//paginator
		$lecturers = $lecturerDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($lecturers));
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
    
    public function addLecturerAction(){
    	$this->view->title="Add Lecturer";
    	
    	$form = new Exam_Form_Lecturer();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$lecturerDB = new App_Model_Exam_DbTable_Lecturer();
				
				try{
					$data = array(
						'name'  => $formData['name'],
						'salutation'  => $formData['salutation'],
						'identity_id'  => $formData['identity_id'],
						'identity_type_id'  => $formData['identity_type_id'],
						'branch_id'  => $formData['branch_id'],
						'country_origin'	=> $formData['country_origin'],
						'email'	=> $formData['email'],
						'status'	=> $formData['status'],				
					);
			
					$lid = $lecturerDB->addData($data);
					
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While add new lecturer");
				}
				
				
				if($lid!=null){
					$this->_helper->flashMessenger->addMessage("Successfuly add new lecturer");
				}
				
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'index'),'default',true));
					
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
    public function editLecturerAction(){
    	$this->view->title="Edit Lecturer";
    	
    	$lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
    	$form = new Exam_Form_Lecturer();
    	
    	$lecturerDB = new App_Model_Exam_DbTable_Lecturer();
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$data = array(
						'name'  => $formData['name'],
						'salutation'  => $formData['salutation'],
						'identity_id'  => $formData['identity_id'],
						'identity_type_id'  => $formData['identity_type_id'],
						'branch_id'  => $formData['branch_id'],
						'country_origin'	=> $formData['country_origin'],
						'email'	=> $formData['email'],
						'status'	=> $formData['status'],				
					);
						
				$lecturerDB->updateData($formData,$lid);
				$this->_helper->flashMessenger->addMessage("Successfuly Update the lecturer's data");
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }else{
    		if($lid!=0){
    			$form->populate($lecturerDB->getData($lid));
    		}
    	}
    	
        $this->view->form = $form;
    }
    
    public function deleteLecturerAction(){
    	$lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
    	if($lid!=0){
    		$lecturerDB = new App_Model_Exam_DbTable_Lecturer();
    		$lecturerDB->deleteData($lid);
    		$this->_helper->flashMessenger->addMessage("Successful deleting lecturer");
    	}else{
    		$this->_helper->flashMessenger->addMessage("Error occurs while deleting user. <br />User not deleted");
    	}
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'index'),'default',true));
    }
    
    public function viewLecturerAction(){
    	
    	$this->view->title="Lecturer's Detail";
    	
    	$lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
    	if($lid!=0){
    		$lecturerDB = new App_Model_Exam_DbTable_Lecturer();
    		$data = $lecturerDB->getData($lid);
    		
    		$this->view->data = $data;
    		
    	}else{
    		$this->_helper->flashMessenger->addMessage("No lecturer data");
			$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'index'),'default',true));
    	}
    	
    	//flash msg
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
    }
    
    public function academicAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
        $this->view->title="Academic Background Record";
        
        $data = new Exam_Model_DbTable_LecturerAcademic();
        $this->view->data = $data->getLecturerData($lid);
        
    }
    
	public function academicAddAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
    	$form = new Exam_Form_LecturerAcademic(array('id'=>$lid));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$academicDB = new Exam_Model_DbTable_LecturerAcademic();
				
				try{
					$data = array(
						'lecturer_id'  => $formData['lecturer_id'],
						'academic_level'  => $formData['academic_level'],
						'year'  => $formData['year'],
						'major'  => $formData['major'],
						'institution'	=> $formData['institution'],
						'entry_date'	=> date('Y-m-d H:i:s'),	
						'verified'	=> 0			
					);
					
					$academicDB->addData($data);
					
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While add new academic record");
				}
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$lid),'default',true)."/#ui-tabs-1");
					
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function academicEditAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$academicDB = new Exam_Model_DbTable_LecturerAcademic();
    	$data = $academicDB->getData($id);
    	
    	$form = new Exam_Form_LecturerAcademic(array('id'=>$data['lecturer_id']));
    	
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$data = array(
						'lecturer_id'  => $formData['lecturer_id'],
						'academic_level'  => $formData['academic_level'],
						'year'  => $formData['year'],
						'major'  => $formData['major'],
						'institution'	=> $formData['institution'],
						'entry_date'	=> date('Y-m-d H:i:s'),	
						'verified'	=> 0,			
				);
						
				$academicDB->updateData($formData,$id);
				$this->_helper->flashMessenger->addMessage("Successfuly Update the lecturer's academic record");
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$data['lecturer_id']),'default',true)."/#ui-tabs-1");		
			}else{
				$form->populate($formData);
			}
        	
        }else{
    		if($id!=0){
    			$form->populate($data);
    		}
    	}
    	
        $this->view->form = $form;
    }
    
	public function academicVerifyAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $id = $this->_getParam('id', 0);
    	$this->view->lid = $id;
    	
    	$academicDB = new Exam_Model_DbTable_LecturerAcademic();
    	$data = $academicDB->getData($id);
    	
    	$academicDB->verify($id);
    	
    	$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$data['lecturer_id']),'default',true)."/#ui-tabs-1");
    }
    
	public function academicDeleteAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$academicDB = new Exam_Model_DbTable_LecturerAcademic();
    	$data = $academicDB->getData($id);
    	
    	$academicDB->deleteData($id);
    	
    	$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$data['lecturer_id']),'default',true)."/#ui-tabs-1");
    }
    
	public function experienceAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
        $this->view->title="Working Experience";
        
        $experienceDB = new Exam_Model_DbTable_LecturerExperience();
        $this->view->data = $experienceDB->getLecturerData($lid);
        
    }
    
	public function experienceAddAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $lid = $this->_getParam('id', 0);
    	$this->view->lid = $lid;
    	
    	$form = new Exam_Form_LecturerExperience(array('id'=>$lid));
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$experienceDB = new Exam_Model_DbTable_LecturerExperience();
				
				try{
					$data = array(
						'lecturer_id'  => $formData['lecturer_id'],
						'organization'  => $formData['organization'],
						'position'  => $formData['position'],
						'subject_taught'  => $formData['subject_taught'],
						'student_academic_level'	=> $formData['student_academic_level'],
						'year_from'	=> $formData['year_from'],	
						'year_to'	=> $formData['year_to']			
					);
					
					$experienceDB->addData($data);
					
				}catch (Exception $e){
					$this->_helper->flashMessenger->addMessage("Error While add new experience record");
				}
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$lid),'default',true)."/#ui-tabs-2");
					
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
    }
    
	public function experienceEditAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$experienceDB = new Exam_Model_DbTable_LecturerExperience();
    	$data = $experienceDB->getData($id);
    	
    	$form = new Exam_Form_LecturerExperience($data['lecturer_id']);
    	
    	
    	if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$data = array(
						'lecturer_id'  => $formData['lecturer_id'],
						'organization'  => $formData['organization'],
						'position'  => $formData['position'],
						'subject_taught'  => $formData['subject_taught'],
						'student_academic_level'	=> $formData['student_academic_level'],
						'year_from'	=> $formData['year_from'],	
						'year_to'	=> $formData['year_to']			
					);
					
						
				$experienceDB->updateData($formData,$id);
				$this->_helper->flashMessenger->addMessage("Successfuly Update the lecturer's academic record");
								
				//redirect
				$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$lid),'default',true)."/#ui-tabs-2");		
			}else{
				$form->populate($formData);
			}
        	
        }else{
    		if($id!=0){
    			$form->populate($data);
    		}
    	}
    	
        $this->view->form = $form;
    }
    
	public function experienceDeleteAction(){
    	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
        $id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$experienceDB = new Exam_Model_DbTable_LecturerExperience();
    	$data = $experienceDB->getData($id);
    	
    	$experienceDB->deleteData($id);
    	
    	$this->_redirect($this->view->url(array('module'=>'exam','controller'=>'lecturer', 'action'=>'view-lecturer','id'=>$lid),'default',true)."/#ui-tabs-2");
    }
}
?>