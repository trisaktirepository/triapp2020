<?php 
class Examapplicant_ExaminationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	 
    	 
    	$this->view->noticeError=$this->_getParam('msg',null);
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	if ($appl_id==202673) {
			$date="2020-01-19";
			$time="13:40:00";
		}
		else {
			$date=date('Y-m-d');
			$time=date('H:s:i');
		}
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbPlacementTest=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	$examdetail=$dbPlacementTest->getActivePtestDetail($appl_id,$date);
    	$dbTestType=new App_Model_Application_DbTable_PlacementTestType();
    	$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	if ($examdetail) {
    	//all test on date
    		$trxid=$examdetail[0]['at_trans_id'];
    		$trx=$dbApplicant->getTransaction($trxid);
    		$this->view->transaction_id=$trxid;
    		 
    		$dbAppPtestDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    		$dbAppTestAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    		$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    		$ptest=$dbPtest->getPtest($trxid);
    		$acid='';
    		$this->view->testtype='';
    		if ($ptest ) { 
    	 		$currenttest=$dbPlacementTest->getActiveTest($trxid, $date, $time);
    			//echo var_dump($currenttest);exit;
    			$acid=$currenttest['app_comp_code'];
    			$this->view->testtype=$acid;
    		}
    		//echo var_dump($comprog);echo '<br>';
    		$trx=$dbApplicant->getTransaction($trxid);
    		//--------get applicant program  -----------
    		$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    		$app_program = $appprogramDB->getPlacementProgram($trxid);
    		 
    		$program_data["program_code1"]="0";
    		$program_data["program_code2"]="0";
    		$program_data["faculty_name2"]="";
    		$program_data["program_name2"]="";
    		 
    		$i=1;$programset='';
    		foreach($app_program as $program){
    			$program_data["program_name".$i] = $program["program_name"];
    			$program_data["faculty_name".$i] = $program["faculty"];
    			$program_data["program_code".$i] = $program["program_code"];
    			if ($programset!='') $programset=$programset.','.$program['program_id'];
    			else $programset=$program['program_id'];
    			$i++;
    		}
    		
    		$timestart="23:00:00";
    		foreach ($examdetail as $key=>$value) {
    			if ($timestart>$value['time_start']) 
    				$timestart=$value['time_start'];
    			$compcode=$value['app_comp_code'];
    			if ($compcode==$acid) {
    				$examdetail[$key]['active']="1";
    			}
    			else $examdetail[$key]['active']="0";
    			$testtype=$dbTestType->getData($compcode);
    			
    			$examdetail[$key]['ptestname']=$testtype['act_name'];
    			
    			$component=$dbExamComp->getDataByComponent($value['apt_ptest_code'], $programset, $compcode);
    			$examdetail[$key]['compcode']=$component;
    			
    		}
    		$this->view->datestart=$date.' '.$timestart;
    	 		
	    		 
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
    
    public function indexTrainingAction()
    {
    
    
    	$this->view->noticeError=$this->_getParam('msg',null);
    	 
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	if ($appl_id==202673) {
    		$date="2020-01-19";
    		$time="13:40:00";
    	}
    	else {
    		$date=date('Y-m-d');
    		$time=date('H:s:i');
    	}
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbPlacementTest=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	$examdetail=$dbPlacementTest->getActivePtestDetail($appl_id,null);
    	$dbTestType=new App_Model_Application_DbTable_PlacementTestType();
    	$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	if ($examdetail) {
    		//all test on date
    		$trxid=$examdetail[0]['at_trans_id'];
    		$trx=$dbApplicant->getTransaction($trxid);
    		$this->view->transaction_id=$trxid;
    		 
    		$dbAppPtestDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    		$dbAppTestAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    		$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    		$ptest=$dbPtest->getPtest($trxid);
    		$acid='';
    		$this->view->testtype='';
    		$trx=$dbApplicant->getTransaction($trxid);
    		//--------get applicant program  -----------
    		$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    		$app_program = $appprogramDB->getPlacementProgram($trxid);
    		 
    		$program_data["program_code1"]="0";
    		$program_data["program_code2"]="0";
    		$program_data["faculty_name2"]="";
    		$program_data["program_name2"]="";
    		 
    		$i=1;$programset='';
    		foreach($app_program as $program){
    			$program_data["program_name".$i] = $program["program_name"];
    			$program_data["faculty_name".$i] = $program["faculty"];
    			$program_data["program_code".$i] = $program["program_code"];
    			if ($programset!='') $programset=$programset.','.$program['program_id'];
    			else $programset=$program['program_id'];
    			$i++;
    		}
    		$timestart="23:00:00";
    		foreach ($examdetail as $key=>$value) {
    			if ($timestart>$value['time_start'])
    				$timestart=$value['time_start'];
    			$compcode=$value['app_comp_code'];
    			if ($compcode==$acid) {
    				$examdetail[$key]['active']="1";
    			}
    			else $examdetail[$key]['active']="0";
    			$testtype=$dbTestType->getData($compcode);
    			
    			$examdetail[$key]['ptestname']=$testtype['act_name'];
    			
    			$component=$dbExamComp->getDataByComponent($value['apt_ptest_code'], $programset, $compcode);
    			$examdetail[$key]['compcode']=$component;
    			
    			
    			 
    		}
    		$this->view->datestart=$date.' '.$timestart;
    		
    
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
    
    
    public function indexTrainingOldAction()
    {
    
    
    	$this->view->noticeError=$this->_getParam('msg',null);
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	 
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbPlacementTest=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	$examdetail=$dbPlacementTest->getActivePtestDetail($appl_id,null);
    	$dbTestType=new App_Model_Application_DbTable_PlacementTestType();
    	$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	if ($examdetail) {
    		//all test on date
    		$trxid=$examdetail[0]['at_trans_id'];
    		$trx=$dbApplicant->getTransaction($trxid);
    		$this->view->transaction_id=$trxid;
    		$compprogram=$dbExamComp->getComponenByTransaction($trxid,"0");
    		foreach ($compprogram as $value) {
    			$comprog[]=$value['ac_id'];
    		}
    		//echo var_dump(compprogram);exit;
    		$dbAppPtestDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    		$dbAppTestAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    		$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    		$ptest=$dbPtest->getPtest($trxid);
    		$acid='';
    		$this->view->testtype='';
    		 
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
    		$this->view->datestart=date('Y-m-d H:s:i');
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
    
	public function verifyExamAction()
    {
        // action body
    	//$this->_helper->layout->setLayout('examapplicant');
    	$trxid=$this->_getParam('idtrx',0);
    	$this->view->title="Examination :";
    	 
    	$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;
		if ($appl_id==202673) {
			$date="2020-01-19";
			$time="13:40:00";
		}
		else {
			$date=date('Y-m-d');
			$time=date('H:s:i');
		}
		//generate personal exam
		$dbTxt=new App_Model_General_DbTable_TmpTxt();
		$dbPtesthead=new App_Model_Application_DbTable_PlacementTest();
		$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
		$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
		$dbAppPtestDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
		$dbAppTestAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$dbPtestDetail=new App_Model_Application_DbTable_PlacementTestDetail();
    	$ptest=$dbPtest->getPtest($trxid);
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
    	if ($ptest ) {
    		
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$currenttest=$dbPestDetail->getActiveTest($trxid, $date, $time);
	    	//echo var_dump($currenttest); 
    		 
	    	if ($currenttest) {
	    		//$dbTxt->add(array('txt'=>'testtye='.$currenttest['app_comp_code']));
	    		$trx=$dbApplicant->getTransaction($trxid);
	    		$compcode=$currenttest['app_comp_code'];
	    		$this->view->testtype=$compcode;
	    		$this->view->testtypecode=$currenttest['initial_code'];
	    		$pstet=$dbPtesthead->getDataByCode($currenttest['apt_ptest_code']);
	    		$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestProgramComponent();
	    		$compprogram=$dbPlacementComp->getComponenByTransaction($trxid, $pstet['aph_testtype']);
	    		$comprog[]='';
	    		foreach ($compprogram as $value) {
	    			$comprog[]=$value['ac_id'];
	    		}
	    		//echo var_dump($pstet);
	    		//echo $compcode;exit;
	    		$component=$dbExamComp->getDataComponent($compcode,$pstet['aph_testtype']);
	    		//echo var_dump($component);exit;
	    		foreach ($component as $idx=>$comp) {
	    		
	    			if (!array_search($comp['ac_id'], $comprog)) {
	    				unset($component[$idx]);
	    				//echo $comp['ac_id'].'<br>';
	    			}
	    		}
	    		$response=$dbAppTestAns->isExamScript($trxid, $compcode);
	    		if (!$response) {
	    		 	//get exam script config
    				//echo var_dump($component); exit;
	    			$dbConfig=new Examapplicant_Model_DbTable_ExamScriptConfig();
	    			$config=$dbConfig->getMatchConfig($currenttest['apt_ptest_code'], $currenttest['apt_aps_id'],$currenttest['app_comp_code']);
	    			//echo var_dump($config);exit;
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
	    							'test_type'=>$currenttest['app_comp_code'],
	    							'token'=>md5(time())
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
	    			
	    		}
	    		if ($response) {
	    			//get number of question per Component
	    			foreach ($component as $idx=>$comp) {
	    				$component[$idx]['jml']=$dbAppTestAns->getNQuestionPerComp($response['apa_id'], $comp['ac_id']);
	    				$ptestcomp=$dbPtestDetail->getPlacementTestComponentData($currenttest['apt_ptest_code'],$comp['ac_comp_code']);
	    				$component[$idx]['nQuestion']=$ptestcomp['apd_total_question'];
	    			}
	    			$this->view->componentlist=$component;
	    		} else $this->_redirect('/examapplicant/examination/index/msg/Fail to generate exam');
	    			
	    		 
	    	} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}  else $this->_redirect('/examapplicant/examination/index/msg/No Test');
    	
		
    }
    
    public function verifyExamTrainingAction()
    {
    	// action body
    	//$this->_helper->layout->setLayout('examapplicant');
    	$trxid=$this->_getParam('idtrx',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	if ($appl_id==202673) {
    		$date="2020-01-19";
    		$time="13:40:00";
    	}
    	else {
    		$date=date('Y-m-d');
    		$time=date('H:s:i');
    	}
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbPtesthead=new App_Model_Application_DbTable_PlacementTest();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    	$dbAppTestAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$dbPtestDetail=new App_Model_Application_DbTable_PlacementTestDetail();
    	$ptest=$dbPtest->getPtest($trxid);
    	$trx=$dbApplicant->getTransaction($trxid);
    	 
    	//--------get applicant program  -----------
    	$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    	$app_program = $appprogramDB->getPlacementProgram($trxid);
    
    	$program_data["program_code1"]="0";
    	$program_data["program_code2"]="0";
    	$program_data["faculty_name2"]="";
    	$program_data["program_name2"]="";
    	$programset='';
    	$i=1;
    	foreach($app_program as $program){
    		$program_data["program_name".$i] = $program["program_name"];
    		$program_data["faculty_name".$i] = $program["faculty"];
    		$program_data["program_code".$i] = $program["program_code"];
    		if ($programset!='') $programset=$programset.','.$program['program_id'];
    		else $programset=$program['program_id'];
    		$i++;
    	}
    	 
    	//-------- get applicant photo --------
    	$photo_name='';
    	$photoDB = new App_Model_Application_DbTable_UploadFile();
    	$photo = $photoDB->getFile($trxid,33); //PHoto
    	 
    	$this->view->transaction=$trx;
    	$this->view->program=$program_data;
    	$this->view->photo=$photo;
    	if ($ptest ) {
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		//$currenttest=$dbPestDetail->getActiveTest($trxid, $date, $time);
    		//echo var_dump($currenttest);
    		 
    		//if ($currenttest) {
    			//$dbTxt->add(array('txt'=>'testtye='.$currenttest['app_comp_code']));
    			$trx=$dbApplicant->getTransaction($trxid);
    			$compcode=$testtype;
    			$this->view->testtype=$compcode;
    			$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);
    			$this->view->testtypecode=$currenttest['initial_code'];
    			$pstet=$dbPtesthead->getDataByCode('TRAINING');
    			$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestComponent();
    			$component=$dbPlacementComp->getDataByComponent($currenttest['apt_ptest_code'], $programset, $testtype);
    			 
    			$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    			if (!$response) {
    				//get exam script config
    				//echo var_dump($component); exit;
    				$dbConfig=new Examapplicant_Model_DbTable_ExamScriptConfig();
    				$config=$dbConfig->getMatchConfig('TRAINING', $currenttest['apt_aps_id'],$currenttest['app_comp_code']);
    				//echo var_dump($config);exit;
    				if ($config) {
    					try {
    						$data=array(
    								'apa_trans_id' => $trx['at_trans_id'],
    								'apa_ptest_code' => $trx['at_pes_id'],
    								'apa_set_code' =>null,
    								'apa_date' => date ('Y-m-d h:i:s'),
    								'pcode' => 'TRAINING',
    								'config'=>$config,
    								'component'=>$component,
    								'test_type'=>$currenttest['app_comp_code'],
    								'token'=>md5(time())
    						);
    						//echo var_dump($data);exit;
    						$dbAppPtest=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    						$response=$dbAppPtest->addData($data);
    
    					} catch (Exception $e) {
    						$msg="Fail to generate Exam Script";
    						$this->_redirect('/examapplicant/examination/index/msg/'.$msg);
    					}
    					 
    				} else $this->_redirect('/examapplicant/examination/index/msg/No Configuration');
    				//get first question
    
    			}
    			if ($response) {
    				//get number of question per Component
    				foreach ($component as $idx=>$comp) {
    					$component[$idx]['jml']=$dbAppTestAns->getNQuestionPerComp($response['apa_id'], $comp['ac_id']);
    					$ptestcomp=$dbPtestDetail->getPlacementTestComponentData($currenttest['apt_ptest_code'],$comp['ac_comp_code']);
    					$component[$idx]['nQuestion']=$ptestcomp['apd_total_question'];
    				}
    				$this->view->componentlist=$component;
    			} else $this->_redirect('/examapplicant/examination/index/msg/Fail to generate exam');
    
    
    		//} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}  else $this->_redirect('/examapplicant/examination/index/msg/No Test');
    	 
    
    }
    
    public function startExamAction()
    {
    	// action body
    	$this->_helper->layout->setLayout('examapplicant');
    	$trxid=$this->_getParam('idtrx',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	if ($appl_id==202673) {
    		$date="2020-01-19";
    		$time="13:40:00";
    	}
    	else {
    		$date=date('Y-m-d');
    		$time=date('H:s:i');
    	}
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	$dbAppTestAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    	 
    	if ($ptest ) {
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$currenttest=$dbPestDetail->getActiveTest($trxid, $date, $time);
    		//echo var_dump($currenttest);
    		 
    		if ($currenttest) {
    			//$dbTxt->add(array('txt'=>'testtye='.$currenttest['app_comp_code']));
    			$trx=$dbApplicant->getTransaction($trxid);
    			$compcode=$currenttest['app_comp_code'];
    			$this->view->testtypecode=$currenttest['initial_code'];
    			$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    			if ($response['last_time']==""){
    				$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestComponent();
    				$components=$dbPlacementComp->getDataByComponent($currenttest['apt_ptest_code'], $programset, $testtype);
    				$component=array();
    				foreach ($components as $idx=>$value) {
    					$questno=$dbAppTestAns->getFirstQuestion($response['apa_id'], $value['ac_id']);
    					$component[$questno]=$value;
    					$component[$questno]['quest_no']=$questno;
    				
    				}
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
    					
    				$token=md5(time());
    				$data=array('token'=>$token);
    				$exammain=$dbAppTestAns->getData($response['apa_id']);
    				if ($exammain['start_time']=='') {
    					$data['start_time']=date('Y-m-d H:i:s');
    					$time=explode(':', $currenttest['timerange']);
    					$data['stop_time']=date('Y-m-d H:i:s',strtotime('+'.$time[0].' hour +'.$time[1].' minutes +'.$time[2].' seconds',strtotime(date('Y-m-d H:i:s'))));
    					$data['last_time']=$data['start_time'];
    					$data['time_rest']=$currenttest['timerange'];
    				} else {  
    					$data['last_time']=date('Y-m-d H:i:s'); 
    					$time=explode(':', $exammain['time_rest']);
    					$data['stop_time']=date('Y-m-d H:i:s',strtotime('+'.$time[0].' hour +'.$time[1].' minutes +'.$time[2].' seconds',strtotime(date('Y-m-d H:i:s'))));
    						
    				}
    				$dbAppTestAns->update($data, 'apa_id='.$response['apa_id']);
    				$exammain=$dbAppTestAns->getData($response['apa_id']);
    				$question['stop_time']=$exammain['stop_time'];
    				$question['token']=$token;
    				$this->view->component=$component;
    				//	echo var_dump($question);
    				$this->view->answer=$answer;
    				$this->view->question=$question;
    				$this->view->n_of_quest=$response['n_of_quest'];
    			} else $this->_redirect('/examapplicant/examination/index/msg/pengguna sudah Login sebelumnya');
    
    	   
    		} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}  else $this->_redirect('/examapplicant/examination/index/msg/No Test');
    	 
    
    }
    
    
    public function generateExamAction()
    {
    	// action body
    	 
    	$trxid=$this->_getParam('idtrx',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	 
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	$dbAppTestAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    
    	if ($ptest ) {
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);
    		//echo var_dump($currenttest);
    		 
    		if ($currenttest) {
    			//$dbTxt->add(array('txt'=>'testtye='.$currenttest['app_comp_code']));
    			$trx=$dbApplicant->getTransaction($trxid);
    			$compcode=$currenttest['app_comp_code'];
    			$this->view->testtypecode=$currenttest['initial_code'];
    			$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    			if ($response) {
    				$dbAppTestAns->updateDataConditional(array('test_type'=>$response['test_type'].rand(100,1000)), 'test_type="'.$compcode.'"  and apa_trans_id='.$trxid);
    			 
    				$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestProgramComponent();
    				$compprogram=$dbPlacementComp->getComponenByTransaction($trxid, "0");
    				$comprog[]='';
    				foreach ($compprogram as $value) {
    					$comprog[]=$value['ac_id'];
    				}
    				$component=$dbExamComp->getDataComponent($compcode);
    				 
    				foreach ($component as $idx=>$comp) {
    
    					if (!array_search($comp['ac_id'], $comprog)) {
    						unset($component[$idx]);
    						//echo $comp['ac_id'].'<br>';
    					}
    				}
    					
    				//get exam script config
    				//echo var_dump($component); exit;
    				$dbConfig=new Examapplicant_Model_DbTable_ExamScriptConfig();
    				$config=$dbConfig->getMatchConfig($currenttest['apt_ptest_code'], $currenttest['apt_aps_id'],$currenttest['app_comp_code']);
    				//echo var_dump($config);exit;
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
    								'test_type'=>$currenttest['app_comp_code'],
    								'token'=>md5(time())
    						);
    						//echo var_dump($data);exit;
    						$dbAppPtest=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    						$response=$dbAppPtest->addData($data);
    
    					} catch (Exception $e) {
    						$msg="Fail to generate Exam Script";
    						$this->_redirect('/examapplicant/examination/index/msg/'.$msg);
    					}
    
    				}  
    				 
    			}
    
    		}  
    
    	}
    	$this->_redirect('/examapplicant/examination/verify-exam/idtrx/'.$trxid);
    
    }
    public function generateExamTrainingAction()
    {
    	// action body
    
    	$trxid=$this->_getParam('idtrx',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    	$dbAppTestAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    
    	if ($ptest ) {
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		//$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);
    		//echo var_dump($currenttest);
    		$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);
    		 
    		//if ($currenttest) {
    			//$dbTxt->add(array('txt'=>'testtye='.$currenttest['app_comp_code']));
    			$trx=$dbApplicant->getTransaction($trxid);
    			$compcode=$currenttest['app_comp_code'];
    			$this->view->testtypecode=$currenttest['initial_code'];
    			$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    			if ($response) {
    				$dbAppTestAns->updateDataConditional(array('test_type'=>$response['test_type'].rand(100,1000)), 'test_type="'.$compcode.'"  and apa_trans_id='.$trxid);
    
    				$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestProgramComponent();
    				$compprogram=$dbPlacementComp->getComponenByTransaction($trxid, "0");
    				$comprog[]='';
    				foreach ($compprogram as $value) {
    					$comprog[]=$value['ac_id'];
    				}
    				
    				if ($compcode=="1") {
    					$compcode="14";
    					$compcodeori="1";
    					 
    				} else $compcodeori="14";
    				$component=$dbExamComp->getDataComponent($compcode);
    				$compcode=$compcodeori;
    				foreach ($component as $idx=>$comp) {
    
    					if (!array_search($comp['ac_id'], $comprog)) {
    						unset($component[$idx]);
    						//echo $comp['ac_id'].'<br>';
    					}
    				}
    					
    				//get exam script config
    				//echo var_dump($component); exit;
    				$dbConfig=new Examapplicant_Model_DbTable_ExamScriptConfig();
    				$config=$dbConfig->getMatchConfig('TRAINING', $currenttest['apt_aps_id'],$currenttest['app_comp_code']);
    				//echo var_dump($config);exit;
    				if ($config) {
    					try {
    						$data=array(
    								'apa_trans_id' => $trx['at_trans_id'],
    								'apa_ptest_code' => $trx['at_pes_id'],
    								'apa_set_code' =>null,
    								'apa_date' => date ('Y-m-d h:i:s'),
    								'pcode' => 'TRAINING',
    								'config'=>$config,
    								'component'=>$component,
    								'test_type'=>$currenttest['app_comp_code'],
    								'token'=>md5(time())
    						);
    						//echo var_dump($data);exit;
    						$dbAppPtest=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    						$response=$dbAppPtest->addData($data);
    
    					} catch (Exception $e) {
    						$msg="Fail to generate Exam Script";
    						$this->_redirect('/examapplicant/examination/index/msg/'.$msg);
    					}
    
    				}
    					
    			//}
    
    		}
    
    	}
    	$this->_redirect('/examapplicant/examination/verify-exam-training/idtrx/'.$trxid.'/testtype/'.$testtype);
    
    }
    
    
    public function startExamTrainingAction()
    {
    	// action body
    	$this->_helper->layout->setLayout('examapplicant');
    	$trxid=$this->_getParam('idtrx',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->testtype=$testtype;
    	$this->view->transaction_id=$trxid;
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	if ($appl_id==202673) {
    		$date="2020-01-19";
    		$time="13:40:00";
    	}
    	else {
    		$date=date('Y-m-d');
    		$time=date('H:s:i');
    	}
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl(); 
    	$dbAppTestAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer(); 
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    	$trx=$dbApplicant->getTransaction($trxid);
    	$this->view->transaction=$trx;
    	if ($ptest ) {
    		
    		$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    		$app_program = $appprogramDB->getPlacementProgram($trxid);
    		 
    		$programset='';
    		 
    		foreach($app_program as $program){
    			 if ($programset!='') $programset=$programset.','.$program['program_id'];
    			else $programset=$program['program_id'];
    			 
    		}
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		//$currenttest=$dbPestDetail->getActiveTest($trxid, $date, $time);
    		//echo var_dump($currenttest);
    		 
    		//if ($currenttest) {
    			//$dbTxt->add(array('txt'=>'testtye='.$currenttest['app_comp_code']));
    			$trx=$dbApplicant->getTransaction($trxid);
    			$compcode=$testtype;
    			$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);
    			
    			//get first question
    			
    			$this->view->testtypecode=$currenttest['initial_code'];
    			$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    			if ($response['last_time']==""){
    				$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestComponent();
    				$components=$dbPlacementComp->getDataByComponent($currenttest['apt_ptest_code'], $programset, $testtype);
    				$component=array();
    				foreach ($components as $idx=>$value) {
    					$questno=$dbAppTestAns->getFirstQuestion($response['apa_id'], $value['ac_id']);
    					$component[$questno]=$value;
    					$component[$questno]['quest_no']=$questno;
    				
    				}
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
    					
    				$token=md5(time());
    				$data=array('token'=>$token);
    				$exammain=$dbAppTestAns->getData($response['apa_id']);
    				if ($exammain['start_time']=='') {
    					$data['start_time']=date('Y-m-d H:i:s');
    					$time=explode(':', $currenttest['timerange']);
    					$data['stop_time']=date('Y-m-d H:i:s',strtotime('+'.$time[0].' hour +'.$time[1].' minutes +'.$time[2].' seconds',strtotime(date('Y-m-d H:i:s'))));
    					$data['last_time']=$data['start_time'];
    					$data['time_rest']=$currenttest['timerange'];
    				} else {  
    					$data['last_time']=date('Y-m-d H:i:s'); 
    					$time=explode(':', $exammain['time_rest']);
    					$data['stop_time']=date('Y-m-d H:i:s',strtotime('+'.$time[0].' hour +'.$time[1].' minutes +'.$time[2].' seconds',strtotime(date('Y-m-d H:i:s'))));
    						
    				}
    				$dbAppTestAns->update($data, 'apa_id='.$response['apa_id']);
    				$exammain=$dbAppTestAns->getData($response['apa_id']);
    				$question['stop_time']=$exammain['stop_time'];
    				$question['token']=$token;
    				//	echo var_dump($question);
    				$this->view->component=$component;
    				$this->view->answer=$answer;
    				$this->view->question=$question;
    				$this->view->n_of_quest=$response['n_of_quest'];
    			} else $this->_redirect('/examapplicant/examination/index/msg/pengguna sudah Login sebelumnya');
    
    	   
    		//} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}  else $this->_redirect('/examapplicant/examination/index/msg/No Test');
    	 
    }
    
    public function reviewExamTrainingAction()
    {
    	// action body
    
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
		}
	
    	 
    	$trxid=$this->_getParam('trxid',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	if ($appl_id==202673) {
    		$date="2020-01-19";
    		$time="13:40:00";
    	}
    	else {
    		$date=date('Y-m-d');
    		$time=date('H:s:i');
    	}
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    	$dbAppTestAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    
    	if ($ptest ) {
    		$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    		$app_program = $appprogramDB->getPlacementProgram($trxid);
    		 
    		$programset='';
    		foreach($app_program as $program){
    			if ($programset!='') $programset=$programset.','.$program['program_id'];
    			else $programset=$program['program_id'];
    
    		}
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$trx=$dbApplicant->getTransaction($trxid);
    		$compcode=$testtype;
    		$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);

    		$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    		
    		$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestComponent();
    		$component=$dbPlacementComp->getDataByComponent($currenttest['apt_ptest_code'], $programset, $testtype);
    		//get first question
    		$components=array();
    		foreach ($component as $idx=>$value) {
    			$answer=$dbAppTestAns->getAnswerQuestion($response['apa_id'],$value['ac_id']);
    			$components[$answer[0]['apad_ques_no']]=$value;
    			$components[$answer[0]['apad_ques_no']]['ans']=$answer;
    		}
    		
    		$this->view->component=$components;    		
    		//} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}   
    
    }
    
    public function reviewExamAction()
    {
    	// action body
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    
    	$trxid=$this->_getParam('trxid',0);
    	$testtype=$this->_getParam('testtype',0);
    	$this->view->title="Examination :";
    
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	if ($appl_id==202673) {
    		$date="2020-01-19";
    		$time="13:40:00";
    	}
    	else {
    		$date=date('Y-m-d');
    		$time=date('H:s:i');
    	}
    	//generate personal exam
    	$dbTxt=new App_Model_General_DbTable_TmpTxt();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbAppPtestDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	$dbAppTestAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    
    	if ($ptest ) {
    		$appprogramDB = new App_Model_Application_DbTable_ApplicantProgram();
    		$app_program = $appprogramDB->getPlacementProgram($trxid);
    		 
    		$programset='';
    		foreach($app_program as $program){
    			if ($programset!='') $programset=$programset.','.$program['program_id'];
    			else $programset=$program['program_id'];
    
    		}
    
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$trx=$dbApplicant->getTransaction($trxid);
    		$compcode=$testtype;
    		$currenttest=$dbPestDetail->getActiveTestByTestType($trxid, $testtype);
    
    		$response=$dbAppTestAns->isExamScript($trxid, $compcode);
    
    		$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestComponent();
    		$component=$dbPlacementComp->getDataByComponent($currenttest['apt_ptest_code'], $programset, $testtype);
    		//get first question
    		$components=array();
    		foreach ($component as $idx=>$value) {
    			$answer=$dbAppTestAns->getAnswerQuestion($response['apa_id'],$value['ac_id']);
    			$components[$answer[0]['apad_ques_no']]=$value;
    			$components[$answer[0]['apad_ques_no']]['ans']=$answer;
    		}
    		
    		$this->view->component=$components;
    
    		//} else $this->_redirect('/examapplicant/examination/index/msg/No Opened Test');
    	}
    
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
    			$answertxt=$dbQuestdetMore->getDataTextByHead($formData['apad_id']);
    			$quest['answertext']=$answertxt['apadm_text'];
    			$answerfiles=$dbQuestdetMore->getDataFileByHead($formData['apad_id']);
    			foreach ($answerfiles as $idx=>$value) {
    				$dt = explode("triapp",$value['pathupload']);
    				$path = $dt[1];
    				$answerfiles[$idx]['pathupload']=$path;
    			}
    			$quest['answerfile']=$answerfiles;
    			 
    		} else {
    			$quest['answerfile']='';
    			$quest['answertext']='';
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
    
    public function ajaxGetQuestionTrainingAction($id=null){
    
    	 
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$quest=array();
    	$dbQuestdet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    	$dbQuestdetMore=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtlMore();
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
    			$answertxt=$dbQuestdetMore->getDataTextByHead($formData['apad_id']);
    			$quest['answertext']=$answertxt['apadm_text'];
    			$answerfiles=$dbQuestdetMore->getDataFileByHead($formData['apad_id']);
    			foreach ($answerfiles as $idx=>$value) {
    				$dt = explode("triapp",$value['pathupload']);
    				$path = $dt[1];
    				$answerfiles[$idx]['pathupload']=$path;
    			}
    			$quest['answerfile']=$answerfiles;
    
    		} else {
    			$quest['answerfile']='';
    			$quest['answertext']='';
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
    	$dbApplAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbQuest=new Examapplicant_Model_DbTable_QuestionBank();
    	$dbQuestdet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		$token=$formData['token'];
    		$row=$dbQuestdet->getData($formData['apad_id']);
    		if ($row['token']==$token) {
	    		$quest=$dbQuest->getQuestion($formData['idQuestion']);
	    		if ($quest['answer_mc']==$formData['answer']) $point=1;else $point=0;
	    		$data=array('apad_appl_ans'=>$formData['answer'],'apad_status_ans'=>$point);
	    		$dbQuestdet->update($data, 'apad_id='.$formData['apad_id']); 
	    		$token=md5(time());
	    		$difftime=date_diff(new DateTime($row['stop_time']),new DateTime( date('Y-m-d H:i:s')));
	    		$resttime=$difftime->format('%H').':'.$difftime->format('%I').':'.$difftime->format('%S');
	    		$dbApplAns->update(array('token'=>$token,'last_time'=>date('Y-m-d H:i:s'),'time_rest'=>$resttime), 'apa_id='.$row['apa_id']);
	    		$quest=$dbQuestdet->getData($formData['apad_id']); 
	    		$quest['error']='0';
    		} else {
    			$quest['error']='1';
    			$quest['msg']='Invalid Token, penyimpanan jawaban gagal, ulangi simpan jawaban';
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
    
    public function ajaxSaveAnswerTrainingAction($id=null){
    
    
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$quest=array();
    	$dbApplAns=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswer();
    	$dbQuest=new Examapplicant_Model_DbTable_QuestionBank();
    	$dbQuestdet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);
    		$token=$formData['token'];
    		
    		$row=$dbQuestdet->getData($formData['apad_id']);
    		//echo var_dump($row);
    		if ($row['token']==$token) {
    			$quest=$dbQuest->getQuestion($formData['idQuestion']);
    			if ($quest['answer_mc']==$formData['answer']) $point=1;else $point=0;
    			$data=array('apad_appl_ans'=>$formData['answer'],'apad_status_ans'=>$point);
    			$dbQuestdet->update($data, 'apad_id='.$formData['apad_id']);
    			$token=md5(time());
    			$dbApplAns->update(array('token'=>$token), 'apa_id='.$row['apa_id']);
    			$quest=$dbQuestdet->getData($formData['apad_id']);
    			$quest['error']='0';
    		} else {
    			$quest['error']='1';
    			$quest['msg']='Invalid Token, penyimpanan jawaban gagal';
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
    	$dbApplAns=new Examapplicant_Model_DbTable_ApplicantPtestAnswer();
    	$dbQuest=new Examapplicant_Model_DbTable_QuestionBank();
    	$dbQuestdet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtlMore();
    	if ($this->getRequest()->isPost()) {
    		 
    		$formData = $this->getRequest()->getPost();
    		$token=$formData['token'];
    		$row=$dbQuestdet->getData($formData['apad_id']);
    		if ($row['token']==$token) {
	    		$quest=$dbQuest->getQuestion($formData['idQuestion']);
	    		//if ($quest['answer_mc']==$formData['answer']) $point=1;else $point=0;
	    		$data=array('apadm_apad_id'=>$formData['apad_id'],'apadm_text'=>$formData['answer'],'created_dt'=>date('Y-m-d H:s:i'),'created_by'=>$appl_id);
	    		$answertext=$dbQuestdet->getData($formData['apad_id']);
	    		if (!$answertext) 
	    			$dbQuestdet->addData($data);
	    		else 
	    			$dbQuestdet->update($data, 'apadm_apad_id='.$answertext['apadm_apad_id']);
	    		$token=md5(time());
	    		$dbApplAns->update(array('token'=>$token), 'apa_id='.$row['apa_id']);
	    		 
	    		$quest=$dbQuestdet->getData($formData['apad_id']);
	    		$quest['error']='0';
    		} else {
    			$quest['error']='1';
    			$quest['msg']='Invalid Token, penyimpanan jawaban gagal';
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
    		//$apaid = $formData['apa_id'];
    		
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
			$flnamenric = $trxid.'_'.date('Ymdhs')."_Usm.png";
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
    
    public function sendStartPhotoAction(){
    
    
    
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
    		$trxid = $formData['trxid'];
    		//$apaid = $formData['apa_id'];
    
    		//$quest=$dbQuestdet->getData($apadid);
    		//$Txt->add(array('txt'=>$trxid));
    		//$Txt->add(array('txt'=>$img));
    		$img = str_replace('data:image/png;base64,', '', $img);
    		$img = str_replace(' ', '+', $img);
    		$fileData = base64_decode($img);
    
    		//$trxid=$quest['apa_trans_id'];
    
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
    		//$dbQuestdet->update(array('apad_auf_id'=>$id), 'apad_id='.$apadid);
    			
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
    	 

    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    	
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$auth = Zend_Auth::getInstance();
    	$quest=array();
    	$dbAnsDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    	 	$apadid=$formData['apadid'];
    	 	$type=$formData['typefile'];
    	 	
    	 	$ans=$dbAnsDet->getData($apadid);
    	 	$trxid=$ans['apa_trans_id'];
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
			$ext_file = $this->getFileExtension($_FILES["file"]["name"]);
			
			//if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG" || $ext_photo == ".pdf" || $ext_photo == ".PDF"){
				$flnamenric = $trxid.'_'.date('Ymdhs')."_Usm".$ext_file;
				$filepath = $applicant_path."/".$flnamenric;
				move_uploaded_file($_FILES["file"]['tmp_name'], $filepath);
				 
			 
			//$Txt->add(array('txt'=>$fileName));
			$upd_photo = array(
							'auf_appl_id' => $trxid,
							'auf_file_name' => $flnamenric,
							'auf_file_type' => $type,
							'auf_upload_date' => date("Y-m-d h:i:s"),
							'auf_upload_by' => $auth->getIdentity()->appl_id,
							'pathupload' => $filepath
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
			$dbAnsDetMore=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtlMore();
			 
			if (!$dbAnsDetMore->isIn($apadid, $id)) {
				$dbAnsDetMore->addData(array('apadm_apad_id'=>$apadid,'apadm_auf_id'=>$id,'created_dt'=>date('Y-m-d H:s:i'),'created_by'=>$auth->getIdentity()->appl_id));
			}
			$quest=$dbAnsDetMore->getDataFileByHead($apadid);
			foreach ($quest as $key=>$value) {
				$dt = explode("triapp",$value['pathupload']);
    			$path = $dt[1];
    			$quest[$key]['pathupload']=$path;
			}
					//$Txt->add(array('txt'=>$id));
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
    
    public function uploadFileTrainingAction() {
    
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    	 
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$auth = Zend_Auth::getInstance();
    	$quest=array();
    	$dbAnsDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$apadid=$formData['apadid'];
    		$type=$formData['typefile'];
    		 
    		$ans=$dbAnsDet->getData($apadid);
    		$trxid=$ans['apa_trans_id'];
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
    		$ext_file = $this->getFileExtension($_FILES["file"]["name"]);
    			
    		//if($ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG" || $ext_photo == ".pdf" || $ext_photo == ".PDF"){
    		$flnamenric = $trxid.'_'.date('Ymdhs')."_Usm".$ext_file;
    		$filepath = $applicant_path."/".$flnamenric;
    		move_uploaded_file($_FILES["file"]['tmp_name'], $filepath);
    			
    
    		//$Txt->add(array('txt'=>$fileName));
    		$upd_photo = array(
    				'auf_appl_id' => $trxid,
    				'auf_file_name' => $flnamenric,
    				'auf_file_type' => $type,
    				'auf_upload_date' => date("Y-m-d h:i:s"),
    				'auf_upload_by' => $auth->getIdentity()->appl_id,
    				'pathupload' => $filepath
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
    		$dbAnsDetMore=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtlMore();
    
    		if (!$dbAnsDetMore->isIn($apadid, $id)) {
    			$dbAnsDetMore->addData(array('apadm_apad_id'=>$apadid,'apadm_auf_id'=>$id,'created_dt'=>date('Y-m-d H:s:i'),'created_by'=>$auth->getIdentity()->appl_id));
    		}
    		$quest=$dbAnsDetMore->getDataFileByHead($apadid);
    		foreach ($quest as $key=>$value) {
    			$dt = explode("triapp",$value['pathupload']);
    			$path = $dt[1];
    			$quest[$key]['pathupload']=$path;
    		}
    		//$Txt->add(array('txt'=>$id));
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
    
    public function deleteFileAction() {
    
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    	 
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$auth = Zend_Auth::getInstance();
    	$quest=array();
    	$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    	$dbAnsDetMore=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtlMore();
    	$dbAnsDet=new Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl();

    	$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    	 
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$apadmid=$formData['apadmid']; 
    		$apadid=$formData['apadid']; 
    		$files=$dbAnsDetMore->getDataById($apadmid);
    		//echo var_dump($files);
    		if ($files) {
    			$apadid=$files['apadm_apad_id'];
    			$fils=$uploadfileDB->getData($files['apadm_auf_id']);
    			if (file_exists($fils['pathupload'])) {
	    			//echo $fils['pathupload'];
		    		if (unlink($fils['pathupload'])) {
		    			 
		    			//$msg= $fils['pathupload']." has been deleted";
		    			$uploadfileDB->deleteData($files['apadm_auf_id']);
		    			$dbAnsDetMore->deleteData($apadmid);
		    		}
    			} else {
    				$uploadfileDB->deleteData($files['apadm_auf_id']);
    				$dbAnsDetMore->deleteData($apadmid);
    			}
    		}
    		 
    		$quest=$dbAnsDetMore->getDataFileByHead($apadid);
    		foreach ($quest as $key=>$value) {
    			$dt = explode("triapp",$value['pathupload']);
    			$path = $dt[1];
    			$quest[$key]['pathupload']=$path;
    		}
    		//$Txt->add(array('txt'=>$id));
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
    
    public function deleteFileTrainingAction() {
    
    
    	if ($this->getRequest()->isXmlHttpRequest()) {
    		$this->_helper->layout->disableLayout();
    	}
    
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    	$auth = Zend_Auth::getInstance();
    	$quest=array();
    	$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    	$dbAnsDetMore=new Examapplicant_Model_DbTable_LathApplicantPtestAnswerDtlMore();
    	$dbAnsDet=new Examapplicant_Model_DbTable_LatihApplicantPtestAnswerDtl();
    
    	$uploadfileDB = new App_Model_Application_DbTable_UploadFile();
    
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		$apadmid=$formData['apadmid'];
    		$apadid=$formData['apadid'];
    		$files=$dbAnsDetMore->getDataById($apadmid);
    		//echo var_dump($files);
    		if ($files) {
    			$apadid=$files['apadm_apad_id'];
    			$fils=$uploadfileDB->getData($files['apadm_auf_id']);
    			if (file_exists($fils['pathupload'])) {
    				//echo $fils['pathupload'];
    				if (unlink($fils['pathupload'])) {
    
    					//$msg= $fils['pathupload']." has been deleted";
    					$uploadfileDB->deleteData($files['apadm_auf_id']);
    					$dbAnsDetMore->deleteData($apadmid);
    				}
    			} else {
    				$uploadfileDB->deleteData($files['apadm_auf_id']);
    				$dbAnsDetMore->deleteData($apadmid);
    			}
    		}
    		 
    		$quest=$dbAnsDetMore->getDataFileByHead($apadid);
    		foreach ($quest as $key=>$value) {
    			$dt = explode("triapp",$value['pathupload']);
    			$path = $dt[1];
    			$quest[$key]['pathupload']=$path;
    		}
    		//$Txt->add(array('txt'=>$id));
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
    	 
     
    function getFileExtension($filename){
    	return substr($filename, strrpos($filename, '.'));
    }

    function generateMark($transud ) {
    	
    }
}

