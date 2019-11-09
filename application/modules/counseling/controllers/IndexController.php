<?php

class Counseling_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
        $Issue = new Counseling_Model_Issues();
        $auth = Zend_Auth::getInstance();
        $registration_id = $auth->getIdentity()->registration_id;
        $this->view->issues = $Issue->getAllByStudent($registration_id,true);

    }

    public function viewAction()
    {
        $id = $this->_getParam('id', null);
        $Issue = new Counseling_Model_Issues();

        $issue = $Issue->find($id);
        $this->view->issue = $issue->current();
    }

    /**
     * get listing of open problems
     */
    public function createAction()
    {
        $post_data = $this->_request->getPost();
        $auth = Zend_Auth::getInstance();
        $Issue = new Counseling_Model_Issues();
        $registration_id = $auth->getIdentity()->registration_id;
        //print_r($post_data);exit;
        if(!empty($post_data['issue'])) {
            $issue_data = $post_data['issue'];
            $issue_data['IdSemesterMain'] = $post_data['IdSemester'];
            $issue_data['IdStudentRegistration'] = $registration_id;
            $issue_data['appt_date'] =  $post_data['appt_date'];
            $issue_data['IdSubject'] =  $post_data['IdSubject'];
            $issue_data['status'] = 0;
            $issue_data['issuedBy'] = $registration_id;
           // $issue_data['dosen_student'] = "0";
            $issue_data['created_at'] = date("Y-m-d H:i:s"); //new
            $Issue->save($issue_data);

            $this->_redirect('/counseling/index/student-view');
        }

        $IssueType = new Counseling_Model_IssueType();
        $this->view->problem_types = $IssueType->getIssueType();
        $this->view->formdate = new Counseling_Form_Issue();
        $dbLandscape=new App_Model_Registration_DbTable_Studentregistration();
        $subjects=$dbLandscape->getLandscape($registration_id);
        $dbSemester=new App_Model_General_DbTable_Semestermaster();
        $this->view->semesterlist=$dbSemester->getCouselingSemester();
        $this->view->formdate->IdSubject->addMultiOptions(array(array('key'=>'*','value'=>'No Subject Relation')));
        foreach ($subjects as $sub) {
        	$this->view->formdate->IdSubject->addMultiOptions(array(array('key'=>$sub['key'],'value'=>$sub['value'])));
        }
    }

public function studentViewAction() {
        //TODO::set appointment page
        $auth = Zend_Auth::getInstance();
        $Issue = new Counseling_Model_Issues();
		$dbSemester=new App_Model_General_DbTable_Semestermaster();
	 	$idstd=$auth->getIdentity()->registration_id;
		$dbIssueDetail=new Counseling_Model_IssuesDetail();
		$dbStaff=new App_Model_General_DbTable_Staffmaster();
		$dbMhs=new App_Model_Registration_DbTable_Studentregistration();
		$Issues=array();
		if ($this->_request->getPost()) {
			$post_data = $this->_request->getPost();
			if (isset($post_data['save'])) {
				//save replay
				$idissue=$post_data['IdIssue'];
				unset($post_data['save']);
				$data=array('answer'=>$post_data['reply'.$idissue],
							'dosen_student'=>'0',
							'dt_answered'=>date("Y-m-d H:i:s"),
							'IdCounselingIssue'=>$idissue,
							'answeredBy'=>$auth->getIdentity()->registration_id,
							'action'=>0
				);
				//echo var_dump($data);exit;
				$dbIssueDetail->add($data);
				//if ($Issue->getActionCode($post_data['action'])=='99') $status="2";else $status="1";
				$Issue->save(array('status'=>"0"),$idissue);
			} else {
				$dbUpload=new Counseling_Model_UploadFile();
				$idsemester=$post_data['IdSemester'];
				$this->view->idsemester=$idsemester;
				$Issues=$Issue->getAllByStudent($auth->getIdentity()->registration_id, $idsemester);
				//echo var_dump($Issues);exit;
				foreach ($Issues as $key=>$value) {
					$idissue=$value['id'];
					$Issues[$key]['Problem']=$value['description'];
					$details=$dbIssueDetail->getByIssueId($value['id']);
					//$Issues[$key]['action']=$Issue->getAction($value['problem_id']);
					foreach ($details as $index=>$det) {
						$idissuedetail=$det['IdCounselingIssueDetail'];
						if ($det['dosen_student']=='1') {
							//dosen
							$namadosen=$dbStaff->getStaffFullName($det['answeredBy']);
							$details[$index]['by']=$namadosen;
						} else {
							//mahasiswa
							$namamhs=$dbMhs->getStudentRegistrationDetail($det['answeredBy']);
							$details[$index]['by']=$namamhs['registrationId'].' - '.$namamhs['appl_fname'].' '.$namamhs['appl_lname'];
						}
						//file upload
						  
						$files=$dbUpload->getData($idissue, $idissuedetail);
						if ($files) $details[$index]['file']=$files; else $details[$index]['file']="";
					}
					$Issues[$key]['Detail']=$details;
				}
			}
			 
		}
		$this->view->semesterlist=$dbSemester->getCountableSemester();
		//$this->view->programlist=$dbProg->getProgramPA($auth->getIdentity()->IdStaff);
        $this->view->issues = $Issues;
    }
    
    public function uploadFileAction() {
    
    
    	// echo "trans=".$txn_id;
    	$auth = Zend_Auth::getInstance();
    	if ($this->_request->getPost()) {
    
    		$formData = $this->getRequest()->getPost();
    		$idissue=$formData['IdIssue'];
    		$idissuedetail=$formData['IdIssueDetail'];
    		$idstd=$formData['IdStudentRegistration'];
    		///upload_file
    		$apath = DOCUMENT_PATH."/counseling";
    		//$apath = "/Users/alif/git/triapp/documents/applicant";
    	  
    		//create directory to locate file
    		if (!is_dir($apath)) {
    			mkdir($apath, 0775);
    		}
    		 
    		///upload_file
    		$applicant_path = DOCUMENT_PATH."/counseling/".date("mY");
    		//$applicant_path = "/Users/alif/git/triapp/documents/applicant/".date("mY");
    	  
    		//create directory to locate file
    		if (!is_dir($applicant_path)) {
    			mkdir($applicant_path, 0775);
    		}
    	  
    	  
    		$major_path = $applicant_path."/".$idissue;
    	  
    		//create directory to locate file
    		if (!is_dir($major_path)) {
    			mkdir($major_path, 0775);
    		}
    	  
    		if (is_uploaded_file($_FILES["file"]['tmp_name'])){
    			$ext_photo = $this->getFileExtension($_FILES["file"]["name"]);
    			 
    			if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG" || $ext_photo == ".pdf" || $ext_photo == ".PDF"){
    				$flnamenric = date('Ymdhs').$ext_photo;
    				$path_photo = $major_path."/".$flnamenric;
    				move_uploaded_file($_FILES["file"]['tmp_name'], $path_photo);
    	    
    				$upd_photo = array(
    						'auf_idStudentRegistration' => $idstd,
    						'idIssueDetail'=>$idissuedetail,
    						'idIssues'=>$idissue,
    						'auf_file_name' => $flnamenric,
    						'auf_file_type' => '',
    						'auf_upload_date' => date("Y-m-d h:i:s"),
    						'auf_upload_by' => $auth->getIdentity()->id,
    						'pathupload' => $path_photo
    				);
    	    
    				$uploadfileDB = new Counseling_Model_UploadFile();
    				 
    				$previous_record = $uploadfileDB->getData($idissue, $idissuedetail);
    				//echo var_dump($upd_photo);
    				if($previous_record){
    					$uploadfileDB->update($upd_photo, $previous_record['auf_id']);
    				} else{
    					$uploadfileDB->save($upd_photo);
    				}
    			}
    			// exit;
    		}
    	  
    		$this->_redirect( $this->baseUrl . $formData['redirect_path']);
    	}
    }
    
    function getFileExtension($filename){
    	return substr($filename, strrpos($filename, '.'));
    }
}

