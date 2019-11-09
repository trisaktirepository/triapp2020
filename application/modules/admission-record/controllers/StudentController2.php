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
		$students = $studentDB->getStudent($student_id);
		
		$this->view->student = $students;
		$app_id = $students["application_id"];
		$landscape_id = $students["landscape_id"];
		
		
		$academicDB = new App_Model_Record_DbTable_AcademicLandscape();
		$landscape = $academicDB->getAcademicLandscape($landscape_id);
		
		$this->view->landscape = $landscape;
		
		
		
		
	}
	
	public function uploadFileAction(){
		
		$this->view->title="Upload File";
    	
    	$form = new AdmissionRecord_Form_UploadFile();
		
	if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                                
//                $upload = new Zend_File_Transfer_Adapter_Http();
//				$upload->setDestination(APPLICATION_PATH."/upload/");
//					try {
//						// upload received file(s)
//						$upload->receive();
//					} catch (Zend_File_Transfer_Exception $e) {
//						$e->getMessage();
//					}
                
                if (!$form->files->receive()) {
                    throw new Zend_File_Transfer_Exception('Reciving files failed');
                }
                
//				$uploadedFilesPaths = $upload->getFileName('doc_path');

                $uploadedFilesPaths = $form->files->getFileName();
                
                if(empty($uploadedFilesPaths)) {
                     $this->view->message = "No files uploaded";
                     return $this->render('finish');
                }
                
                // single uploaded file will not be an array. So make it to array.
                if (!is_array($uploadedFilesPaths)) {
                    $uploadedFilesPaths = (array) $uploadedFilesPaths;
                }
                
                
                // because this is only a demo so immidiately remove the files
                foreach ($uploadedFilesPaths as $file) {
                    if (!unlink($file)) {
                        throw new Exception('Cannot remove file: ' . $file);
                    }
                }
                 
                
                // everything went fine so go to success action
                // this script is executed inside the iframe.
               // echo '<script>window.top.location.href = "'.$this->view->baseUrl().'/admission-record/student/success'.'";</script>';
                exit;
                
            }
        }
        $form->setAction($this->view->baseUrl('/admission-record/student/upload-file'));
        $this->view->form = $form;
    }

    public function progressAction() {

        // check if a request is an AJAX request
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new Zend_Controller_Request_Exception('Not an AJAX request detected');
        }

        $uploadId = $this->getRequest()->getParam('id');

        // this is the function that actually reads the status of uploading
        $data = uploadprogress_get_info($uploadId);

        $bytesTotal = $bytesUploaded = 0;

        if (null !== $data) {
            $bytesTotal = $data['bytes_total'];
            $bytesUploaded = $data['bytes_uploaded'];
        }

        $adapter = new Zend_ProgressBar_Adapter_JsPull();
        $progressBar = new Zend_ProgressBar($adapter, 0, $bytesTotal, 'uploadProgress');

        if ($bytesTotal === $bytesUploaded) {
            $progressBar->finish();
        } else {
            $progressBar->update($bytesUploaded);
        }
    }

    public function successAction() {
  
    }



	
//	public function uploadFileAction(){
//		//title
//    	$this->view->title="Upload File";
//    	
//    	$form = new AdmissionRecord_Form_UploadFile();
//		
//		if ($this->getRequest()->isPost()) {
//			
//			$formData = $this->getRequest()->getPost();
//			if ($form->isValid($formData)) {
//				
//		
//				/* Uploading Document File on Server */
//			
//				$upload = new Zend_File_Transfer_Adapter_Http();
//				$upload->setDestination(APPLICATION_PATH."/upload/");
//					try {
//						// upload received file(s)
//						$upload->receive();
//					} catch (Zend_File_Transfer_Exception $e) {
//						$e->getMessage();
//					}
//				
//    
//				// so, Finally lets See the Data that we received on Form Submit
//				$uploadedData = $form->getValues();
//				Zend_Debug::dump($uploadedData, 'Form Data:');
//				
//				// you MUST use following functions for knowing about uploaded file
//				# Returns the file name for 'doc_path' named file element
//				$name = $upload->getFileName('doc_path');
//				
//				# Returns the size for 'doc_path' named file element
//				# Switches of the SI notation to return plain numbers
////				$upload->setOption(array('useByteString' => false));
//				$size = $upload->getFileSize('doc_path');
////				$size = $upload->getFileSize('doc_path');
//				
//				# Returns the mimetype for the 'doc_path' form element
//				$mimeType = $upload->getMimeType('doc_path');
//				
//					$adapter = new Zend_ProgressBar_Adapter_Console();
//				    $progressBar = new Zend_ProgressBar($adapter, 0, $size);
//     
//				    while (!feof($progressBar)) {
//				        // Do something
//				     
//				        $progressBar->update($currentByteCount);
//				    }
//				     
//				    $progressBar->finish();
//				
//				// following lines are just for being sure that we got data
//				echo "Name of uploaded file: $name<br>
//				";
//				echo "File Size: $size<br>
//				";
//				echo "File's Mime Type: $mimeType<br>";
//				
////				$adapter = new Zend_ProgressBar_Adapter_Console();
////				$upload2  = Zend_File_Transfer_Adapter_Http::getProgress($adapter);
////				
//			
//
//
//				
////				$this->view->fileName = $name;
////		$this->view->fileSize = $size;
////		$this->view->fileType = $mimeType;
//				
//				//$this->_redirect($this->view->url(array('module'=>'admission-record','controller'=>'student', 'action'=>'upload-file'),'default',true));
//				
//				
////				exit;
//				
//				
//				
//				// New Code For Zend Framework :: Rename Uploaded File
////				$renameFile = 'newName.jpg';
////				
////				$fullFilePath = '/images/'.$renameFile;
////				
////				// Rename uploaded file using Zend Framework
////				$filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));
////				
////				$filterFileRename -> filter($name);
//				
//				
//			
//			}else{
//				$form->populate($formData);
//			}
//        	
//        }
//        
//    	
//        $this->view->form = $form;
//		
//		
//	}


	
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