<?php
/**
 * 
 * @author Muhamad Alif
 * @version 
 */


class AjaxUtilityController extends Zend_Controller_Action {
	
	protected $country_tbl = 'tbl_countries ';
	protected $state_tbl = 'tbl_state';
	protected $city_tbl = 'tbl_city';
	protected $family_tbl = 'applicant_family';
	
	public function getStateAction($country_id=0){
    	$country_id = $this->_getParam('country_id', 0);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('s'=>$this->state_tbl))
	                 ->where('s.idCountry = ?', $country_id)
	                 ->order('s.StateName ASC');
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
	public function getCityAction($country_id=0){
    	$state_id = $this->_getParam('state_id', 0);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('c'=>$this->city_tbl))
	                 ->where('c.idState = ?', $state_id)
	                 ->order('c.CityName ASC');
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
    
	public function getFamilyInfoAction(){
		
    	$appl_id = $this->_getParam('appl_id', 0);
    	$relation = $this->_getParam('relation', 0);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('f'=>$this->family_tbl))
	                 ->where('f.af_appl_id = ?', $appl_id)
	                 ->where('f.af_relation_type = ?', $relation);	                
	    
        $row = $db->fetchRow($select);
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
    
    public function ajaxGetLocationAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
    	$select_date = $this->_getParam('select_date', 0);
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        $applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	$location_list = $applicantPlacementScheduleDB->getLocationByDate($select_date);
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($location_list);
		echo $json;
		exit();

    }
    
    

	public function ajaxGetFeesAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
		$transaction_id = $this->_getParam('transaction_id', 0);
    	$code = $this->_getParam('code', 0);
    	$program = $this->_getParam('program', 0);
    	$location = $this->_getParam('location', 0);
    	$lid = $this->_getParam('lid', 0);//location id    	
    	
    	$program_fee=0;
    	$location_fee=0;
    	
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
		//get Fees by Program
    	if($program==1){
    		
    		//1st:check how many program apply.    		
    		$ptestDB = new App_Model_Application_DbTable_ApplicantProgram();	
    		$list_program = $ptestDB->getPlacementProgram($transaction_id);
    		$total_program_apply = count($list_program);
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'PROGRAM','value'=>$total_program_apply);
    		$fees_info = $feeDB->getFees($condition);
    		$program_fee = $fees_info["apfs_amt"];
    	}
    	
		//get Fees by location
    	if($location==1){
    		
    		//1st:check where is the location.    		
    		$location_id = $lid;
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'LOCATION','value'=>$location_id);
    		$fees_info = $feeDB->getFees($condition);
    		$location_fee = $fees_info["apfs_amt"];
    	}
    	
    	$total_fees = abs($program_fee)+abs($location_fee);
    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($total_fees);
		echo $json;
		exit;

    }
    
    
	public function ajaxGetDisciplineAction(){
    	$school_type_id = $this->_getParam('school_type_id', 2);

     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('sd'=>'school_discipline'),array('smd_code','smd_desc'))
	                 ->where('sd.smd_school_type = ?', $school_type_id)
	                 ->order('sd.smd_desc ASC');
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
    
public function ajaxGetSchoolAction(){
    	$school_type_id = $this->_getParam('school_type_id', 0);
    	$school_state_id = $this->_getParam('school_state_id', 0);
    	$keyvalue = $this->_getParam('keyvalue', 0);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	
	  	if($keyvalue==1){
		  	$select = $db->select()
		                 ->from(array('sm'=>'school_master'),array('sm_id','sm_name'))
		                 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
	  	}else{
	  		$select = $db->select()
		                 ->from(array('sm'=>'school_master'))
		                 ->order('sm.sm_name ASC');
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
	  	}
	    
	    
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
	public function ajaxGetSchoolSortAction(){
    	$school_type_id = $this->_getParam('school_type_id', 0);
    	$school_state_id = $this->_getParam('school_state_id', 0);
    	$keyvalue = $this->_getParam('keyvalue', 0);
    	$sort = $this->_getParam('sort', null);
    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	
	  	if($keyvalue==1){
		  	$select = $db->select()
		                 ->from(array('sm'=>'school_master'),array('sm_id','sm_name','sm_school_code'));
		                 
		    if($sort!=null){
		    	$select->order('sm.'.$sort);
		    }else{
		    	$select->order('sm.sm_name ASC');
		    }
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
	  	}else{
	  		$select = $db->select()
		                 ->from(array('sm'=>'school_master'));
		               
	  		if($sort!=null){
		    	$select->order('sm.'.$sort);
		    }else{
		    	$select->order('sm.sm_name ASC');
		    }
		    
		    if($school_type_id!=0){
		    	$select->where('sm.sm_type = ?', $school_type_id);
		    }
			if($school_state_id!=0){
		    	$select->where('sm.sm_state = ?', $school_state_id);
		    }
	  	}
	    
	    
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
public function ajaxGetProgrammePtAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	
        $this->_helper->layout->disableLayout();
        
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        //program in placement test with discipline filter

        //transaction data
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
        $transaction_data= $transDB->getTransactionData($transaction_id);
    	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get placement test data
		$select = $db->select(array('apt_ptest_code'))
	                 ->from(array('ap'=>'applicant_ptest'))
	                 ->where('ap.apt_at_trans_id = ?', $transaction_id);
	                 
	    $stmt = $db->query($select);
        $placementTestCode = $stmt->fetch();
        
        //get placementest program data filtered with discipline
	  	$select = $db->select()
	                 ->from(array('app'=>'appl_placement_program'))
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('ArabicName','ProgramName','ProgramCode','IdProgram') )
	                 ->join(array('apr'=>'appl_program_req'),"apr.apr_program_code = app.app_program_code and apr.apr_decipline_code = '".$discipline_code."' and apr.apr_academic_year = ".$transaction_data['at_academic_year'])
	                 ->where('app.app_placement_code  = ?', $placementTestCode['apt_ptest_code'])
	                 ->order('p.ArabicName ASC');
				
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		
		if($row){
        	$json = Zend_Json::encode($row);
        }else{
        	$json = null;
        }
		
		echo $json;
		exit();
    }
    
public function ajaxGetProgrammeHsAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	$academic_year = $this->_getParam('academic_year', 0);
    	
        $this->_helper->layout->disableLayout();
        
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        //program in placement test with discipline filter

        //transaction data
		$auth = Zend_Auth::getInstance();
		$appl_id = $auth->getIdentity()->appl_id;    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$transDB = new App_Model_Application_DbTable_ApplicantTransaction();
        $transaction_data= $transDB->getTransactionData($transaction_id);
    	
		$db = Zend_Db_Table::getDefaultAdapter();
		
	                 
		//get transaction data
		$select = $db->select()
	                 ->from(array('at'=>'applicant_transaction'))
	                 ->where('at.at_trans_id = ?', $transaction_id);
	                 
	    $stmt = $db->query($select);
        $transactionData = $stmt->fetch();
        
        $select_applied = $db->select()
         			 ->from(array('at'=>'applicant_transaction'),array())
	                 ->join(array('ap'=>'applicant_program'),'ap.ap_at_trans_id=at.at_trans_id',array('ap_prog_code'=>'distinct(ap.ap_prog_code)'))
	                 ->where("at.at_appl_id= '".$appl_id."'")
	                 ->where("ap.ap_at_trans_id != '".$transaction_id."'")
	                 ->where("at.at_appl_type=2");	                 
	               

        //get program data based on discipline
	  	$select = $db->select()
	                 ->from(array('apr'=>'appl_program_req'))
	                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = apr.apr_program_code' )
	                 ->where("p.ProgramCode NOT IN (?)",$select_applied)
	                 ->order('p.ArabicName ASC');
	                 
	    if($discipline_code!=0){
	    	$select->where('apr.apr_decipline_code  = ?', $discipline_code);
	    }
	    
		if($academic_year!=0){
	    	$select->where('apr.apr_academic_year  = ?', $academic_year);
	    }             
				
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		
		if($row){
        	$json = Zend_Json::encode($row);
        }else{
        	$json = null;
        }
		
		echo $json;
		exit();
    }
    
    
	public function ajaxGetDisciplineSubjectAction(){
    	$discipline_code = $this->_getParam('discipline_code', 0);
    	    	
     	//if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        //}
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
        // $auth = Zend_Auth::getInstance(); 
    	//$appl_id = $auth->getIdentity()->appl_id;
    	$appl_id = $this->_getParam('appl_id', 0);
    	
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	
	  	//get applicant education head data
	  	$applicationEducationDb = new App_Model_Application_DbTable_ApplicantEducation();
	  	$educationData = $applicationEducationDb->getDataByapplID($appl_id);
	  	
	  	$select = $db->select()
	                 ->from(array('sds'=>'school_decipline_subject'))
	                 ->where('sds.sds_discipline_code  = ?', $discipline_code)
	                 ->join(array('s'=>'school_subject'),'s.ss_id = sds.sds_subject')
	                 //->joinLeft(array('aed'=>'applicant_education_detl'),'aed.')
	                 ->order('s.ss_core_subject DESC')
	                 ->order('s.ss_subject ASC');
	                 
	    if($educationData){
	    	$select->joinLeft(array('aed'=>'applicant_education_detl'),"aed.aed_ae_id = ".$educationData['ae_id']." and  aed.aed_subject_id = sds.sds_subject");
	    }
	    	   
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
    
	public function ajaxGetTestFeesAction($id=null){

	 	$storage = new Zend_Auth_Storage_Session ();
		$data = $storage->read ();
		if (! $data) {
			$this->_redirect ( 'index/index' );
		}
			
		$transaction_id = $this->_getParam('transaction_id', 0);
    	$code = $this->_getParam('code', 0);
    	$program = $this->_getParam('program', 0);
    	$location = $this->_getParam('location', 0);    	
    	$aps_id = $this->_getParam('aps_id', 0);//aps id
    	
    	$program_fee=0;
    	$location_fee=0;   	
     
     	if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
        
        $applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	$shedule_info = $applicantPlacementScheduleDB->getData($aps_id);
    	$lid=$shedule_info["aps_location_id"];
    	
		//get Fees by Program
    	if($program==1){
    		
    		//1st:check how many program apply.    		
    		$ptestDB = new App_Model_Application_DbTable_ApplicantProgram();	
    		$list_program = $ptestDB->getPlacementProgram($transaction_id);
    		$total_program_apply = count($list_program);
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'PROGRAM','value'=>$total_program_apply,'aptcode'=>$code);
    		$fees_info = $feeDB->getFees($condition);
    		$program_fee = $fees_info["apfs_amt"];
    	}
    	
		//get Fees by location
    	if($location==1){
    		
    		//1st:check where is the location.    		
    		$location_id = $lid;
    		
    		$feeDB = new App_Model_Application_DbTable_PlacementFeeSetup();
    		$condition = array('type'=>'LOCATION','value'=>$location_id,'aptcode'=>$code);
    		$fees_info = $feeDB->getFees($condition);
    		$location_fee = $fees_info["apfs_amt"];
    	}
    	
    	$total_fees = abs($program_fee)+abs($location_fee);
    	
    	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($total_fees);
		echo $json;
		exit;

    }
    

}

