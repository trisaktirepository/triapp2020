<?php 
class Examapplicant_ExaminationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	 
    	$this->view->title="Examination Day";
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id; 
    	if ($appl_id==202673) $date="2020-01-19";
    	else $date=date('Y-m-d');
    	$dbApplicant=new App_Model_Application_DbTable_ApplicantTransaction();
    	$dbExamComp=new App_Model_Application_DbTable_PlacementTestComponent();
    	$dbPlacementTest=new App_Model_Application_DbTable_ApplicantPtestDetail();
    	$examdetail=$dbPlacementTest->getActivePtestDetail($appl_id,$date);
    	if ($examdetail) {
    		foreach ($examdetail as $key=>$value) {
    			$compcode=$value['app_comp_code'];
    			$component=$dbExamComp->getDataComponent($compcode);
    			$examdetail[$key]['compcode']=$component;
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
        
    	$this->_helper->layout->setLayout('exam');
    	$trxid=$this->_getParam('idtrx',0);
    	$this->view->title="Examination :";
    	 
    	$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;
		if ($appl_id==202673) {
			$date="2020-01-19";
			$time="09:00:00";
		}
		else {
			$date=date('Y-m-d');
			$time=date('h:s:i');
		}
    	//generate personal exam
    	$dbPtest=new App_Model_Application_DbTable_ApplicantPtest();
    	$ptest=$dbPtest->getPtest($trxid);
    	if ($ptest) {
    		$dbPestDetail=new App_Model_Application_DbTable_ApplicantPtestDetail();
    		$currenttest=$dbPestDetail->getActiveTest($trxid, $date, $time);
    		if ($currenttest) {
    			$appcomp=$currenttest['app_comp_code'];
    			$dbPlacementComp=new App_Model_Application_DbTable_PlacementTestProgramComponent();
    			$comps=$dbPlacementComp->getComponenByTransaction($trxid, "0");
    			foreach ($comps as $comp) {
    				$this->view->title=$this->view->title.', '.$comp['ac_comp_name_bahasa'];
			
    			}
    			$this->view->starttest="1";
    		} else $this->view->starttest="0";
    	}
    	
		
    }
    
 	public function ajaxSaveStartExamAction($id=null){
    	
    	$idSchedule = $this->_getParam('idSche', 0);
    	$idCourse = $this->_getParam('idCourse', 0);
    	$idCenter = $this->_getParam('idCenter', 0);
    	$idStart = $this->_getParam('idStart', 0);
    	echo $id = $this->_getParam('id', 0);
    	
    	
    	// check is AJAX request or not
     	/*if (!$this->getRequest() -> isXmlHttpRequest()) {
        	$this->getResponse() -> setHttpResponseCode(404)
                              -> sendHeaders();
         	$this->renderScript('empty.phtml');
         	return false;
     	}*/
    	
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        $time = date('H:i:s');
        
		$startDB = new App_Model_Exam_DbTable_StartExam();
		
		if($id==1){
			$data = array(
		        'idCourse' => $idCourse,
		        'idSchedule' => $idSchedule,
		        'idCenter' => $idCenter,
		        'startTime' => $time
	        );
        
			$idInsert = $startDB->add($data);
		}else{
			$dataClose = array(
		        'endTime' => $time
	        );
        	$idInsert = $idStart;
			$updateData = $startDB->updateData($dataClose,$idStart);
		}

		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($idInsert);
		
		$this->view->json = $json;

    }


}

