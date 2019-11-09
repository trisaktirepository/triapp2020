<?php

class AdmissionRecord_StudentController extends Zend_Controller_Action {
	
	public function profileAction(){
		//title
    	$this->view->title="Student Profile";
    	
    	
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
    						'fullname'=>$search_fullname,
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

	public function viewProfileAction(){
		//title
    	$this->view->title="Student Profile";
    	
    	$student_id= $this->_getParam('id', 0);
    	$this->view->student_id = $student_id;
    	
    	$studentDB = new App_Model_Record_DbTable_Student();
		$students = $studentDB->getStudentProfile($student_id);		
		$this->view->student = $students;
		
		$app_id       = $students["application_id"];
		$landscape_id = $students["landscape_id"];
		$ic_no        = $students["ic_no"];
		
		//get intake name
	    $oIntake =  new App_Model_Record_DbTable_Intake();
	    $intake = $oIntake->getIntake($students["admission_intake_id"]);
	    $this->view->intake_name = $intake["name"];
	    
	       
		
		$academicDB = new App_Model_Record_DbTable_AcademicLandscape();
		$landscape = $academicDB->getAcademicLandscape($landscape_id);
		
		$this->view->landscape = $landscape;
		
		
		/*--------------------
		  CORRESPONDENCE ADRESS
		  -------------------- */
		
		$oAddress = new App_Model_Record_DbTable_StudentAddress();
		$address  = $oAddress->getAddress($student_id,1); 
		$this->view->c_address = $address;
		
		/*--------------------
		  PERMENANT ADRESS
		  -------------------- */
		
		$oAddress = new App_Model_Record_DbTable_StudentAddress();
		$addressp  = $oAddress->getAddress($student_id,2); 
		$this->view->p_address = $addressp;
		
		/*--------------------
		  CONTACT
		  -------------------- */
		
		$oContact = new App_Model_Record_DbTable_StudentContact();
		$contact  = $oContact->getContact($student_id);
		$this->view->contact = $contact;
		
		
		/*--------------------
		  REGISTRATION HISTORY
		  -------------------- */
		$oRegHistory = new App_Model_Record_DbTable_StudentCourseRegistrationHistory();
		$info = $oRegHistory->getRegistrationHistoryData($student_id);
		$this->view->info = $info;
		
		
		/*--------------------
		  EXAM SLIP
		  -------------------- */
    	$student_semester = new App_Model_Record_DbTable_StudentSemester();    	   		
    	$student_semester_list = $student_semester->getStudentSemester($student_id);
    	$this->view->semester_list=$student_semester_list;
		
		
		/*--------------------
		  UPLOAD FILE
		  -------------------- */
		$uploadFileDb = new App_Model_Record_DbTable_UploadFile();
		$fileupload = $uploadFileDb->getDataPicture($student_id);
		
		$this->view->uploadFile = $fileupload;
		
		
		$form = new AdmissionRecord_Form_UploadFile();
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				
				//echo APPLICATION_PATH; // /data/apps/icampus/application
				$thisdir ="/data/apps/sis_oum/public/documents/Upload/UserFiles/profile";
				
				if(mkdir($thisdir ."/$ic_no" , 0777)) 
				{ 
				   echo "Directory has been created successfully..."; 
				} 
				else 
				{ 
				   echo "Failed to create directory..."; 
				
				
				}
				
//				echo $thisdir2 = getcwd(); 
//				
//				
//				exit();
//				
				$uploadDir = $thisdir ."/$ic_no/";
								
				//exit();
		
				/* Uploading Document File on Server */
			
				$upload = new Zend_File_Transfer_Adapter_Http();
				$upload->setDestination($uploadDir);
					try {
						// upload received file(s)
						$upload->receive();
					} catch (Zend_File_Transfer_Exception $e) {
						$e->getMessage();
					}
				
    
//					$adapter = new Zend_ProgressBar_Adapter_Console();
//					$upload2  = Zend_File_Transfer_Adapter_Http::getProgress($adapter);
//						
//					while (!$upload2['done']) {
//					    $upload2 = Zend_File_Transfer_Adapter_Http::getProgress($upload2);
//					}

				// so, Finally lets See the Data that we received on Form Submit
//				$uploadedData = $form->getValues();
//				Zend_Debug::dump($uploadedData, 'Form Data:');
				
				// you MUST use following functions for knowing about uploaded file
				# Returns the file name for 'doc_path' named file element
				$name = $upload->getFileName('doc_path');
				
				# Returns the size for 'doc_path' named file element
				# Switches of the SI notation to return plain numbers
//				$upload->setOption(array('useByteString' => false)); //with error
				$size = $upload->getFileSize('doc_path');
//				$size = $upload->getFileSize('doc_path');
				
				# Returns the mimetype for the 'doc_path' form element
				$mimeType = $upload->getMimeType('doc_path');
				
				// following lines are just for being sure that we got data
				echo "<br><br>Name of uploaded file: $name<br>
				";
				echo "File Size: $size<br>
				";
				echo "File's Mime Type: $mimeType<br>";
				
			
//				Zend_Debug::dump($_FILES);
//				print_r($_FILES);

//				echo array($_FILES["name"]);	

				//rename part
				$piecesName = explode(".", $name);
				$file_type = $piecesName[1];
				$date = date('Ymd_his');
				
				
//				 New Code For Zend Framework :: Rename Uploaded File
				echo $renameFile = $ic_no.'_'.$date.'.'.$file_type;
				echo "<br>";
				echo $fullFilePath = $uploadDir.$renameFile;
				echo "<br>";
				// Rename uploaded file using Zend Framework
				$filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));
				
				$filterFileRename -> filter($name);
				
				//insert into database
				$data = array(
						'student_id'=>$student_id,
						'filename'=>$renameFile,
						'location'=>$uploadDir,
						'dateupload'=>date('Y-m-d H:i:s')
						);
						
				$idpic = $uploadFileDb->addData($data);
				
				//update pic id into student database
				$students = $studentDB->updatePic($student_id,$idpic);
				
				
				$this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'student', 'action'=>'view-profile', 'id'=>$student_id),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
        
    	
        $this->view->form = $form;
		
		
		
		
	}
	
	public function uploadFileAction(){
		//title
    	$this->view->title="Upload File";
    	
    	$form = new AdmissionRecord_Form_UploadFile();
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
		
				/* Uploading Document File on Server */
			
				$upload = new Zend_File_Transfer_Adapter_Http();
				$upload->setDestination(APPLICATION_PATH."/upload/");
					try {
						// upload received file(s)
						$upload->receive();
					} catch (Zend_File_Transfer_Exception $e) {
						$e->getMessage();
					}
				
    
				// so, Finally lets See the Data that we received on Form Submit
//				$uploadedData = $form->getValues();
//				Zend_Debug::dump($uploadedData, 'Form Data:');
				
				// you MUST use following functions for knowing about uploaded file
				# Returns the file name for 'doc_path' named file element
				$name = $upload->getFileName('doc_path');
				
				# Returns the size for 'doc_path' named file element
				# Switches of the SI notation to return plain numbers
//				$upload->setOption(array('useByteString' => false)); //with error
				$size = $upload->getFileSize('doc_path');
//				$size = $upload->getFileSize('doc_path');
				
				# Returns the mimetype for the 'doc_path' form element
				$mimeType = $upload->getMimeType('doc_path');
				
				// following lines are just for being sure that we got data
				echo "Name of uploaded file: $name<br>
				";
				echo "File Size: $size<br>
				";
				echo "File's Mime Type: $mimeType<br>";
				
//				$this->view->fileName = $name;
//		$this->view->fileSize = $size;
//		$this->view->fileType = $mimeType;
				
				//$this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'student', 'action'=>'upload-file'),'default',true));
				
				
//				exit;
				
				
				
				// New Code For Zend Framework :: Rename Uploaded File
//				$renameFile = 'newName.jpg';
//				
//				$fullFilePath = '/images/'.$renameFile;
//				
//				// Rename uploaded file using Zend Framework
//				$filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));
//				
//				$filterFileRename -> filter($name);
				
				
			
			}else{
				$form->populate($formData);
			}
        	
        }
        
    	
        $this->view->form = $form;
		
		
	}
	
	
	
	function addressAction(){
		
		if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
		
		$this->view->title="Edit Address"; 

		$studentID = $this->_getParam('id', 0);
    	$this->view->studentID = $studentID;
    	
    	$address_type_id = $this->_getParam('address_type_id', 0);
    	$this->view->address_type_id = $address_type_id;
    	    	
    	$form = new AdmissionRecord_Form_Address(array('studentID'=>$studentID,'addresstypeID'=>$address_type_id));

		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {				
	    			
				$oAddress = new App_Model_Record_DbTable_StudentAddress(); 
			   	$oAddress->updateData($formData,$studentID,$address_type_id);
				
				$this->_redirect('/admission-record/student/view-profile/id/'.$studentID);
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($studentID>0){    	
    			
    			$oAddress = new App_Model_Record_DbTable_StudentAddress();    			   			
    			$form->populate($oAddress->getAddress($studentID,$address_type_id));    			
    		}
    		
    	}
     
        $this->view->form = $form;
	}
	
	
	public function contactAction(){
		
		if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
		
		$this->view->title="Edit Student Contact"; 		

		$studentID = $this->_getParam('id', 0);
    	$this->view->studentID = $studentID;
    	    	
    	$form = new AdmissionRecord_Form_Contact(array('studentID'=>$studentID));
    	 
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {				
	    		
				$oContact= new App_Model_Record_DbTable_StudentContact(); 
				$oContact->updateData($formData,$studentID);
				
				$this->_redirect('/admission-record/student/view-profile/id/'.$studentID);
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($studentID>0){
    			
    		    $oContact= new App_Model_Record_DbTable_StudentContact(); 
    			$form->populate($oContact->getContact($studentID));
    			
    		}
    		
    	}
    	
    	$this->view->form = $form;
	}
	
	
	
	
	
//	function addAction()
//	{
//		
//		$form = new DocumentForm();
//		$this->view->form = $form;
//		
//		if ($this->_request->isPost()) {
//		
//			$formData = $this->_request->getPost();
//			if ($form->isValid($formData)) {
//			
//			/* Uploading Document File on Server */
//			$upload = new Zend_File_Transfer_Adapter_Http();
//			$upload->setDestination("/uploads/files/");
//			try {
//			// upload received file(s)
//			$upload->receive();
//			} catch (Zend_File_Transfer_Exception $e) {
//			$e->getMessage();
//			}
//			
//			// so, Finally lets See the Data that we received on Form Submit
//			$uploadedData = $form->getValues();
//			Zend_Debug::dump($uploadedData, 'Form Data:');
//			
//			// you MUST use following functions for knowing about uploaded file
//			# Returns the file name for 'doc_path' named file element
//			$name = $upload->getFileName('doc_path');
//			
//			# Returns the size for 'doc_path' named file element
//			# Switches of the SI notation to return plain numbers
//			$upload->setOption(array('useByteString' => false));
//			$size = $upload->getFileSize('doc_path');
//			
//			# Returns the mimetype for the 'doc_path' form element
//			$mimeType = $upload->getMimeType('doc_path');
//			
//			// following lines are just for being sure that we got data
//			print "Name of uploaded file: $name
//			";
//			print "File Size: $size
//			";
//			print "File's Mime Type: $mimeType";
//			
//			// New Code For Zend Framework :: Rename Uploaded File
//			$renameFile = 'newName.jpg';
//			
//			$fullFilePath = '/images/'.$renameFile;
//			
//			// Rename uploaded file using Zend Framework
//			$filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));
//			
//			$filterFileRename -> filter($name);
//			
//			exit;
//			} 
//		
//		} else {
//		
//		// this line will be called if data was not submited
//		$form->populate($formData);
//		}
//	}

}
?>