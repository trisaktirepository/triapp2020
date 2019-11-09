<?php
class Application_IndexController extends Zend_Controller_Action
{

	public function indexAction(){
	
		$this->view->title = "Application List";
				
		$applicantDB = new App_Model_Application_DbTable_ApplicantProfile();
		
	
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$applicant_data = $applicantDB->search($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($applicant_data));
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$applicant_data = $applicantDB->getPaginateData();
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($applicant_data));
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
    	
	} 
	
	
	
	public function listAction(){
	
		$this->view->title = "Online Application (Student Application)";
				
		$studentDB = new App_Model_Record_DbTable_Student();	
	
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			$student_data = $studentDB->searchList($formData);
			
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($student_data));
			$paginator->setItemCountPerPage(100);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
			
		}else{
			$student_data = $studentDB->getPaginateData(1);
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($student_data));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page',1));
		}
		
		$this->view->paginator = $paginator;
		
    	
	} 
	
public function addAction()
    {
    	//title
    	$this->view->title="Add New Applicant";
    	
    	$form = new Application_Form_Manual();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$manual = new App_Model_Record_DbTable_Student();
				$manual->addData($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));		
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Edit Applicant";
    	
    	$form = new Application_Form_Manual();
    	    	
    	$this->view->form = $form;
    	
    	$student_id = $this->_getParam('id', 0);
    	
    	$this->view->id = $student_id;
    	
    	$manual = new App_Model_Record_DbTable_Student(); 
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
//				echo " masuk";
				
				$manual->updateData($formData,$student_id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($student_id>0){
    			$manual = new App_Model_Record_DbTable_Student();
    			$form->populate($manual->getData($student_id));
    		}
    		
    	}
    	
    	$attachmentDB = new App_Model_Application_DbTable_UploadAttachment();
    	$attachment = $attachmentDB->getData($student_id);
    	$this->view->attachment = $attachment;
    	/*--------------------
		  UPLOAD FILE
		  -------------------- */
    	
    	$applicant = $manual->getApplication($student_id);
    	$ic_no = $applicant["ARD_IC"];
    	
    	$this->view->ic_no = $ic_no;
		$uploadFileDb = new App_Model_Application_DbTable_UploadAttachment();
		$fileupload = $uploadFileDb->getData($student_id);
		
		$this->view->uploadFile = $fileupload;
		
		$form = new Application_Form_UploadAttachment();
		
		if ($this->getRequest()->isPost()) {
			
			$filename = $this->getRequest()->getPost('filename');
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				
				//echo APPLICATION_PATH; // /data/apps/icampus/application
				$thisdir ="/data/apps/sis_oum/public/documents/Application";
				
				if(mkdir($thisdir ."/$ic_no" , 0777)) 
				{ 
				   echo "Directory has been created successfully..."; 
				} 
				else 
				{ 
				   echo "Failed to create directory..."; 
				
				
				}
				
				$uploadDir = $thisdir ."/$ic_no/";
								
				/* Uploading Document File on Server */
			
				$upload = new Zend_File_Transfer_Adapter_Http();
				$upload->setDestination($uploadDir);
					try {
						// upload received file(s)
						$upload->receive();
					} catch (Zend_File_Transfer_Exception $e) {
						$e->getMessage();
					}
				
				// you MUST use following functions for knowing about uploaded file
				# Returns the file name for 'doc_path' named file element
				$name = $upload->getFileName('doc_path'); //Name of uploaded file
				
				# Returns the size for 'doc_path' named file element
				# Switches of the SI notation to return plain numbers
				
//				$upload->setOption(array('useByteString' => false)); //with error
				$size = $upload->getFileSize('doc_path'); // File Size
				
				# Returns the mimetype for the 'doc_path' form element
				$mimeType = $upload->getMimeType('doc_path'); //File's Mime Type
				
//				Zend_Debug::dump($_FILES);
//				print_r($_FILES);

//				echo array($_FILES["name"]);	

				//rename part
				$piecesName = explode(".", $name);
				$file_type = $piecesName[1];
				$date = date('Ymd_His');
				
				
//				 New Code For Zend Framework :: Rename Uploaded File
				$renameFile = $ic_no.'_'.$date.'.'.$file_type;
				$fullFilePath = $uploadDir.$renameFile;
				
				// Rename uploaded file using Zend Framework
				$filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));
				
				$filterFileRename -> filter($name);
				
				//insert into database
				$data = array(
						'app_id'=>$student_id,
						'filename'=>$filename,
						'fileupload'=>$renameFile,
						'dateupload'=>date('Y-m-d H:i:s')
						);
						
				$idpic = $uploadFileDb->addData($data);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'edit', 'id'=>$student_id),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->formUpload = $form;
      
    }
    
    public function offerAction(){
    	//title
    	$this->view->title="Offer Program";
    	
    	$form = new Application_Form_Manual();
    	    	
    	$this->view->form = $form;
    	
    	echo $id = $this->_getParam('id', 0);
    	
    	$manual = new App_Model_Record_DbTable_Student(); 
		$applicant = $manual->getData($id);
		$this->view->applicant = $applicant;
		
		$appliedDB = new App_Model_Application_DbTable_AppliedProgram();
		$applied= $appliedDB->getAppliedProgram($id);
		$this->view->applied = $applied;
//		
//    	if ($this->getRequest()->isPost()) {
//    		
//    		$formData = $this->getRequest()->getPost();
//    		
//	    	if ($form->isValid($formData)) {
//				
//				$manual = new App_Model_Record_DbTable_Student(); 
//				$manual->updateData($formData,$id);
//				
//				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
//			}else{
//				$form->populate($formData);	
//			}
//    	}else{
//    		if($id>0){
//    			$manual = new App_Model_Record_DbTable_Student();
//    			$form->populate($manual->getData($id));
//    		}
//    		
//    	}
    }
    
    public function viewAction(){

		if ($this->getRequest()->isPost()) {
			$formData = $_POST;
			
		}else{
			//title
	    	$this->view->title="Manual Entry";
	    	
	    	$id = $this->_getParam('id', 0);
	    	$this->view->id = $id;
	    	
	    	if($id==0){
	    		$this->view->noticeError = "Unknown Applicant";
	    	}else{
	    		//get course details
	    		$applicantDB = new App_Model_Record_DbTable_Student();
	    		$this->view->applicant = $applicantDB->getNewApplication($id);
	    		
	    		//get branch list
	    		$branchDB = new App_Model_General_DbTable_Branch();
	    		$this->view->branchlist = $branchDB->getData();
	    		
	    		//get intake list
	    		$intakeDB = new App_Model_Record_DbTable_Intake();
	    		$this->view->intakelist = $intakeDB->getIntake(); 
	    	}
		}
		
		
		
        
        
	}
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$manual = new App_Model_Record_DbTable_Student();
    		$manual->deleteData($id);
    	}
    	
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'index'),'default',true));
    	
    }
    
    public function deleteAttachmentAction($id = null){
    	$id = $this->_getParam('id', 0);
    	$student_id = $this->_getParam('app_id', 0);
    	
    	//unlink('documents/Application/910629085797/910629085797_20110712_055933.jpg');
    	
    	if($id>0){
    		$uploadDB = new App_Model_Application_DbTable_UploadAttachment();
    		
    		$upload = $uploadDB->getDataUpload($student_id,$id);
    		$ic = $upload["ARD_IC"];
    		$fileupload = $upload["fileupload"];
    		$path = "documents/Application/".$ic."/".$fileupload;

    		//delete physical file
    		if(is_file($path)){
				unlink($path);
			}
    		
    		//delete from database
    		$uploadDB->deleteData($id);
    		
    	}
    	
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'manual', 'action'=>'edit', 'id'=>$student_id),'default',true));
    	
    }
}
?>