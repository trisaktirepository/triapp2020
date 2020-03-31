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
			$time=date('h:s:i');
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
	    					$answer[$value['apad_quest_no']]=$value['apad_appl_ans'];
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
    

     
}

