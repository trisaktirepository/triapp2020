<?php 
class Examapplicant_ExaminationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$msg=$this->_getParam('msg');
    	$this->view->title="Examination ".$msg;
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	if ($appl_id==202673) $date="2020-01-19";
    	else $date=date('Y-m-d');
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbPlacementTest=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	$examdetail=$dbPlacementTest->getActivePtestDetail($appl_id,$date);
    	$dbTestType=new App_Model_Application_DbTable_PlacementTestType();
    	if ($examdetail) {
    	//all test on date
    		$trxid=$examdetail[0]['at_trans_id'];
    		$trx=$dbApplicant->getTransaction($trxid);
    		$this->view->transaction_id=$trxid;
    		$compprogram=$dbExamComp->getComponenByTransaction($trxid,"0");
    		foreach ($compprogram as $value) {
    			$comprog[]=$value['ac_id'];
    		}
    		//echo var_dump($comprog);echo '<br>';
    		foreach ($examdetail as $key=>$value) {
    			
    			$compcode=$value['app_comp_code'];
    			$component=$dbExamComp->getDataComponent($compcode,'0');
    			//echo var_dump($component);
    			foreach ($component as $idx=>$comp) {
    				if (array_keys($comprog,$comp['ac_id'])==array())
    					unset($component[$idx]);
    			}
    			$testtype=$dbTestType->getData($compcode);
    			$examdetail[$key]['compcode']=$component;
    			$examdetail[$key]['ptestname']=$testtype['act_name'];
    			
    		}
    		$trxid=$examdetail[0]['at_trans_id'];
    		$trx=$dbApplicant->getTransaction($trxid);
    		//--------get applicant program  -----------
    		$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    		$app_program = $appprogramDB->getPlacementProgram($trxid);
    		
    		$program_data["program_code1"]="0";
    		$program_data["program_code2"]="0";
    		$program_data["faculty_name2"]="";
    		$program_data["program_name2"]="";
    		
    		$i=1;
    		foreach($app_program as $program){
    			$program_data["program_name".$i] = $program["program_name"];
    			$program_data["faculty_name".$i] = $program["faculty"];
    			$program_data["program_code".$i] = $program["program_code"];
    		
    			$i++;
    		}
    		 
    		//-------- get applicant photo --------
    		$photo_name='';
    		$photoDB = new App_Model_Application_DbTable_UploadFile();
    		$photo = $photoDB->getFile($trxid,33); //PHoto
    		 
    		$this->view->transaction=$trx;
    		$this->view->program=$program_data;
    		$this->view->photo=$photo;
    		$this->view->examdetail=$examdetail;
    		$this->view->test="1";
    	} else $this->view->test="0";
    	
    }
    
	public function startExamAction()
    {
        // action body
    	$this->_helper->layout->setLayout('examapplicant');
    	$trxid=$this->_getParam('idtrx',0);
    	$this->view->title="Examination :";
    	 
    	$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;
		if ($appl_id==202673) {
			$date="2020-01-19";
			$time="9:00:00";
		}
		else {
			$date=date('Y-m-d');
			$time=date('H:s:i');
		}
		//generate personal exam
		$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
		$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
		$dbAppPtestDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
		$dbAppTestAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    	
    	if ($ptest ) {
    		
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$currenttest=$dbPestDetail->getActiveTest($trxid, $date, $time);
	    	if ($currenttest) {
	    		
	    		$trx=$dbApplicant->getTransaction($trxid);
	    		$compcode=$currenttest['app_comp_code'];
	    		$this->view->testtypecode=$currenttest['initial_code'];
	    		$response=$dbAppTestAns->isExamScript($trxid, $compcode);
	    		if (!$response) {
	    			$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestProgramComponent();
	    			$compprogram=$dbPlacementComp->getComponenByTransaction($trxid, "0");
	    			foreach ($compprogram as $value) {
	    				$comprog[]=$value['ac_id'];
	    			}
	    			$component=$dbExamComp->getDataComponent($compcode);
	    			foreach ($component as $idx=>$comp) {
    					if (array_keys($comprog,$comp['ac_id'])==array())
    						unset($component[$idx]);
    					}
	    			//get exam script config
	    			//echo var_dump($currenttest);echo var_dump($component);exit;
	    			$dbConfig=new Examapplicant_Model_DbTable_ExamScriptConfig();
	    			$config=$dbConfig->getMatchConfig($currenttest['apt_ptest_code'], $currenttest['apt_aps_id'],$currenttest['app_comp_code']);
	    			if ($config) {
	    				try {
	    					$data=array(
	    							'apa_trans_id' => $trx['at_trans_id'],
	    							'apa_ptest_code' => $trx['at_pes_id'],
	    							'apa_set_code' =>null,
	    							'apa_date' => date ('Y-m-d h:i:s'),
	    							'pcode' => $currenttest['apt_ptest_code'],
	    							'config'=>$config,
	    							'component'=>$component,
	    							'test_type'=>$currenttest['app_comp_code']
	    							);
	    					//echo var_dump($data);exit;
	    					$dbAppPtest=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
	    					$response=$dbAppPtest->addData($data);
	    					
	    				} catch (Exception $e) {
	    					$msg="Fail to generate Exam Script";
	    					$this->_redirect('/examapplicant/examination/index/msg/'.$msg);
	    				}
	    				
	    			} else $this->_redirect('/examapplicant/examination/index/msg/No Configuration');
	    			//get first question
	    			if ($response) {
	    				$answerset=$dbAppPtestDet->getDataByHead($response['apa_id']);
	    				foreach ($answerset as $value) {
	    					$answer[$value['apad_ques_no']]=$value['apad_appl_ans'];
	    				}
	    				$question=$dbAppPtestDet->getQuestionBySequence($response['apa_id'], 1);
	    				$dt = explode("triapp",$question['question_url']);
	    				$path = $dt[1];
	    				$question['question_url']=$path;
	    				if ($question['question_parent_url']!='') {
	    					$dt = explode("triapp",$question['question_parent_url']);
	    					$path = $dt[1];
	    					$question['question_parent_url']=$path;
	    				}
	    				$question['stop_time']=date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) + strtotime($currenttest['timerange']));
	    				//echo var_dump($question);
	    				//echo var_dump($currenttest);
	    				$exammain=$dbAppTestAns->update(array('start_time'=>date('Y-m-d H:i:s'),'stop_time'=>$question['stop_time']), 'apa_id='.$response['apa_id']);
	    				$this->view->question=$question;
	    				$this->view->answer=$answer;
	    				$this->view->n_of_quest=$response['n_of_quest'];
	    			} else $this->_redirect('/examapplicant/examination/index/msg/Fail to generate exam');
	    			
	    		} else {
	    			$answerset=$dbAppPtestDet->getDataByHead($response['apa_id']);
	    			foreach ($answerset as $value) {
	    				$answer[$value['apad_ques_no']]=$value['apad_appl_ans'];
	    			}
	    			$question=$dbAppPtestDet->getQuestionBySequence($response['apa_id'], 1);
	    			$dt = explode("triapp",$question['question_url']);
	    			$path = $dt[1];
	    			$question['question_url']=$path;
	    			if ($question['question_parent_url']!='') {
	    				$dt = explode("triapp",$question['question_parent_url']);
	    				$path = $dt[1];
	    				$question['question_parent_url']=$path;
	    			}
	    			$exammain=$dbAppTestAns->getData($response['apa_id']);
	    			$question['stop_time']=$exammain['stop_time'];
	    			$this->view->answer=$answer;
	    			$this->view->question=$question;
	    			$this->view->n_of_quest=$response['n_of_quest'];
	    		}
	    	
	    		
	    	} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}  else $this->_redirect('/examapplicant/examination/index/msg/No Test');
    	
		
    }
    
 	 

    public function ajaxGetQuestionAction($id=null){
    	 
     
    	 
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$quest=array();
    	$dbQuestdet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	$dbQuestdetMore=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtlMore();
    	if ($this->getRequest()->isPost()) {
    	
    		$formData = $this->getRequest()->getPost();
    		$quest=$dbQuestdet->getQuestionBySequence($formData['apa_id'], $formData['order']);
    		$dt = explode("triapp",$quest['question_url']);
    		$path = $dt[1];
    		$quest['question_url']=$path;
    		if ($quest['question_parent_url']!='') {
    			$dt = explode("triapp",$quest['question_parent_url']);
    			$path = $dt[1];
    			$quest['question_parent_url']=$path;
    		}
    		$answernonmc=$dbQuestdetMore->getData($formData['apad_id']);
    		if ($answernonmc) {
    			$quest['answertext']=$answernonmc['apadm_text'];
    			if ($answernonmc['apadm_auf_id']!='') {
    				$dbUplFile=new App_Model_Application_DbTable_UploadFile();
    				$files=$dbUplFile->getData($answernonmc['apadm_auf_id']);
    				$dt = explode("triapp",$quest['pathupload']);
    				$path = $dt[1];
    				$quest['answerfile']=$path;
    			}
    		}
    	}
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    		
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($quest);
    
    	echo $json;
    	exit();
    
    }
    
    public function ajaxSaveAnswerAction($id=null){
    
    	 
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$quest=array();
    	$dbQuest=new Examapplicant_Model_DbTable_QuestionBank();
    	$dbQuestdet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		$quest=$dbQuest->getQuestion($formData['idQuestion']);
    		if ($quest['answer_mc']==$formData['answer']) $point=1;else $point=0;
    		$data=array('apad_appl_ans'=>$formData['answer'],'apad_status_ans'=>$point);
    		$dbQuestdet->update($data, 'apad_id='.$formData['apad_id']); 
    		$quest=$dbQuestdet->getData($formData['apad_id']);   
    	}
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($quest);
    
    	echo $json;
    	exit();
    
    }
    public function ajaxSaveAnswerTextAction($id=null){
    
    
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$quest=array();
    	$dbQuest=new Examapplicant_Model_DbTable_QuestionBank();
    	$dbQuestdet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtlMore();
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		$quest=$dbQuest->getQuestion($formData['idQuestion']);
    		//if ($quest['answer_mc']==$formData['answer']) $point=1;else $point=0;
    		$data=array('apadm_apad_id'=>$formData['apad_id'],'apadm_text'=>$formData['answer'],'created_dt'=>date('Y-m-d H:s:i'),'created_by'=>$appl_id);
    		$answertext=$dbQuestdet->getData($formData['apad_id']);
    		if (!$answertext) 
    			$dbQuestdet->addData($data);
    		else 
    			$dbQuestdet->update($data, 'apadm_apad_id='.$answertext['apadm_apad_id']);
    		$quest=$dbQuestdet->getData($formData['apad_id']);
    	}
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($quest);
    
    	echo $json;
    	exit();
    
    }
    
    
    public function sendPhotoAction(){
    
    
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    	$auth = Zend_Auth::getInstance();
    	
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$quest=array();
    	$Txt=new App_Model_General_DbTable_TmpTxt();
    	$dbQuest=new Examapplicant_Model_DbTable_QuestionBank();
    	$dbTrx=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbQuestdet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$img = $formData['image'];
    		$type = $formData['type'];
    		$apadid = $formData['apad_id'];
    		
    		$quest=$dbQuestdet->getData($apadid);
    		//$Txt->add(array('txt'=>$trxid));
    		//$Txt->add(array('txt'=>$img));
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$fileData = base64_decode($img);
			 
			$trxid=$quest['apa_trans_id'];
			 
			///upload_file
			$apath = DOCUMENT_PATH."/applicant";
			//$apath = "/Users/alif/git/triapp/documents/applicant";
			
			//create directory to locate file
			if (!is_dir($apath)) {
				mkdir($apath, 0775);
			}
			
			///upload_file
			$applicant_path = DOCUMENT_PATH."/applicant/USM/".date("mY")."/".$trxid;
			//$applicant_path = "/Users/alif/git/triapp/documents/applicant/".date("mY");
			
			//create directory to locate file
			if (!is_dir($applicant_path)) {
				mkdir($applicant_path, 0775,true);
			}
			$flnamenric = date('Ymdhs')."_Usm.png";
			$fileName = $applicant_path."/".$flnamenric;
			file_put_contents($fileName, $fileData);
			//$Txt->add(array('txt'=>$fileName));
			$upd_photo = array(
							'auf_appl_id' => $trxid,
							'auf_file_name' => $flnamenric,
							'auf_file_type' => $type,
							'auf_upload_date' => date("Y-m-d h:i:s"),
							'auf_upload_by' => $auth->getIdentity()->appl_id,
							'pathupload' => $fileName
			);
			
			
			$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
			
			$previous_record = $uploadfileDB->getFile($trxid,$type);
					//echo var_dump($previous_record);
			if($previous_record){
				$id=$previous_record['auf_id'];
				$uploadfileDB->updateData($upd_photo,$id);
			}else{
				$id=$uploadfileDB->addData($upd_photo);
				
			}
			$dbQuestdet->update(array('apad_auf_id'=>$id), 'apad_id='.$apadid);
			
					//$Txt->add(array('txt'=>$id));
			 
    	}
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($upd_photo);
    
    	echo $json;
    	exit();
    
    }
    public function uploadFileAction() {
    	 
    	$trxid = $this->_getParam('trxid',null);
    	$apadid = $this->_getParam('apad_id',null);
    	$type = $this->_getParam('type',null);
    	 
    	// echo "trans=".$txn_id;
    	$auth = Zend_Auth::getInstance();
    
    	$formData = $this->getRequest()->getPost();
    
    	$DocumentUploads = new App_Model_General_DbTable_Maintenance();
    	$checklist = $DocumentUploads->fnGetMaintenanceDisplay($formData['type_id']);
    
    	///upload_file
    	$apath = DOCUMENT_PATH."/applicant";
    	//$apath = "/Users/alif/git/triapp/documents/applicant";
    
    	//create directory to locate file
    	if (!is_dir($apath)) {
    		mkdir($apath, 0775);
    	}
    
    	///upload_file
    	$applicant_path = DOCUMENT_PATH."/applicant/".date("mY");
    	//$applicant_path = "/Users/alif/git/triapp/documents/applicant/".date("mY");
    
    	//create directory to locate file
    	if (!is_dir($applicant_path)) {
    		mkdir($applicant_path, 0775);
    	}
    
    
    	$major_path = $applicant_path."/".$txn_id;
    
    	//create directory to locate file
    	if (!is_dir($major_path)) {
    		mkdir($major_path, 0775);
    	}
    
    	if (is_uploaded_file($_FILES["file"]['tmp_name'])){
    		$ext_photo = $this->getFileExtension($_FILES["file"]["name"]);
    
    		if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG" || $ext_photo == ".pdf" || $ext_photo == ".PDF"){
    			$flnamenric = date('Ymdhs')."_".$checklist['idDefinition'].$ext_photo;
    			$path_photo = $major_path."/".$flnamenric;
    			move_uploaded_file($_FILES["file"]['tmp_name'], $path_photo);
    
    			$upd_photo = array(
    					'auf_appl_id' => $txn_id,
    					'auf_file_name' => $flnamenric,
    					'auf_file_type' => $formData['type_id'],
    					'auf_upload_date' => date("Y-m-d h:i:s"),
    					'auf_upload_by' => $auth->getIdentity()->appl_id,
    					'pathupload' => $path_photo
    			);
    
    
    			$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    
    			$previous_record = $uploadfileDB->getFile($formData["transaction_id"],$formData['type_id']);
    			echo var_dump($previous_record);
    			if($previous_record){
    				$uploadfileDB->updateData($upd_photo,$previous_record['auf_id']);
    			}else{
    				$uploadfileDB->addData($upd_photo);
    			}
    		}
    		//exit;
    	}
    
    	$this->_redirect( $this->baseUrl . $formData['redirect_path']);
    }

     
}

