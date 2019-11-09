<?php
/**
 * @author Ajaque Rahman
 * @version 1.0
 */

class Agent_BatchAgentController extends Zend_Controller_Action {
	
	private $_DbObj;
	private $_config;
	
	public function init(){
		
/*		$sis_session = new Zend_Session_Namespace('sis');
		$configDb = new GeneralSetup_Model_DbTable_Initialconfiguration();
		$this->_config = $configDb->fnGetInitialConfigDetails($sis_session->idUniversity);*/
		
		
	}
	
	public function indexAction() {
		//title
    	$this->view->title= $this->view->translate("Batch Application : Agent");
    	$form = new Agent_Form_BatchAgent();
    	$this->view->form = $form;    	
	}
	public function uploadOmrAction(){
		//title
		$ptform = new Agent_Form_Ptest();
		$this->view->ptform = $ptform; 
    	$this->view->title= $this->view->translate("Batch Application - Upload Answer OMR");
    	$badb = new App_Model_Application_DbTable_Batchagent();	
    	
    	$form = new Agent_Form_BatchAgent();
    	
    	if ($this->_request->isPost()) {

       		$formData = $this->_request->getPost();
       		//print_r($formData);
            // success - do something with the uploaded file
            $uploadedData = $form->getValues();
            $fullFilePath = $form->file->getFileName();
            
            //echo $fullFilePath;
			$file = fopen($fullFilePath, "r") or exit("Unable to open file!");
				//Output a line of the file until the end is reached
			$i=0;
			$data = array();
			$transactionDB  = new App_Model_Application_DbTable_ApplicantTransaction();
			$profileDB  = new App_Model_Application_DbTable_ApplicantProfile();
			$familyDB  = new App_Model_Application_DbTable_ApplicantFamily();
			$aprogDB  = new App_Model_Application_DbTable_ApplicantProgram();
			$appeduDB = new App_Model_Application_DbTable_ApplicantEducation();
			while(!feof($file)){
				$line_data = fgets($file); 
				
				if(substr($line_data,40,8)!=""){
					$at_pes_id=substr($line_data,40,8);
			     	
				    $appl_info=$transactionDB->uniqueApplicantid( $at_pes_id );
					//for app profile table
					if($appl_info){
						$data[$i]["status"]="Not Valid";
					}else{
						$data[$i]["status"]="Saved";
					}
					$data[$i]["appl_fname"] = substr($line_data,97,25);
					$data[$i]["appl_gender"] = substr($line_data,186,1);
					$data[$i]["appl_gender"] = $badb->getlookupID("SEX",$data[$i]["appl_gender"]);
					$data[$i]["appl_religion"] = substr($line_data,187,1);//sis_setup_detail
					$data[$i]["appl_religion"] = $badb->getlookupID("RELIGION",$data[$i]["appl_religion"]);
					$data[$i]["appl_dob"] = substr($line_data,79,6);
					$data[$i]["citycode_dob"] = substr($line_data,87,3);		
					$data[$i]["appl_nationality"] = substr($line_data,93,1);
					
					$data[$i]["appl_phone_home"] = substr($line_data,147,6);
					$data[$i]["appl_phone_hp"] = substr($line_data,62,12);
					
					$data[$i]["appl_address1"] = substr($line_data,122,25);
					$data[$i]["appl_postcode"] = substr($line_data,180,5);					
					$data[$i]["cityname"] = substr($line_data,153,27)	;
					$data[$i]["appl_state"] = substr($line_data,74,2);				
					$data[$i]["appl_state"] = $badb->getstateID($data[$i]["appl_state"]);
					$data[$i]["appl_city"] = substr($line_data,76,3);	
					$data[$i]["appl_city"] = $badb->getcityID($data[$i]["appl_state"],$data[$i]["appl_city"]);
										
					//for app family table
					$data[$i]["father_af_name"] = substr($line_data,237,25);										
					$data[$i]["father_af_address1"] = substr($line_data,287,25);
					$data[$i]["father_af_postcode"] = substr($line_data,324,5);
					$data[$i]["father_af_state"] = substr($line_data,332,2);
					$data[$i]["father_af_state"] = $badb->getstateID($data[$i]["father_af_state"]);
					$data[$i]["father_af_city"] = substr($line_data,329,3);
					$data[$i]["father_af_city"] = $badb->getcityID($data[$i]["father_af_state"],$data[$i]["father_af_city"]);
					
					$data[$i]["father_af_family_condition"] = substr($line_data,198,1);
					$data[$i]["father_af_family_condition"] = $badb->getlookupID("PARENTCON",$data[$i]["father_af_family_condition"]);	
					$data[$i]["father_af_education_level"] = substr($line_data,201,1); //sis_setup_dtl
					$data[$i]["father_af_education_level"] = $badb->getlookupID("EDULEVEL",$data[$i]["father_af_education_level"]);					
					$data[$i]["father_af_job"] = substr($line_data,203,1); //tbl familyjob					
					$data[$i]["father_af_phone"] = substr($line_data,312,6);
										

					$data[$i]["mother_af_name"] = substr($line_data,262,25);
					$data[$i]["mother_af_family_condition"] = substr($line_data,199,1);
					$data[$i]["mother_af_family_condition"] = $badb->getlookupID("PARENTCON",$data[$i]["mother_af_family_condition"]);
					$data[$i]["mother_af_education_level"] = substr($line_data,202,1);
					$data[$i]["mother_af_education_level"] = $badb->getlookupID("EDULEVEL",$data[$i]["mother_af_education_level"]);	
					$data[$i]["mother_af_job"] = substr($line_data,204,1);	

					
 
										
					//education
					$data[$i]["hsyearpass"] = substr($line_data,206,6);
					$data[$i]["ae_institution"] = substr($line_data,223,8);		//schoolmaster//	
					$data[$i]["ae_institution"] = $badb->gethsID($data[$i]["ae_institution"]);		
					$data[$i]["ae_discipline_code"] = substr($line_data,212,3);
					$data[$i]["hsprovince"] = substr($line_data,215,2);
					$data[$i]["hsprovince"] = $badb->getstateID($data[$i]["hsprovince"]);
					$data[$i]["hscitycode"] = substr($line_data,217,3);
					$data[$i]["hscitycode"] = $badb->getcityID($data[$i]["hsprovince"],$data[$i]["hscitycode"]);
				
					//for txn table
					$data[$i]["applicantID"] = substr($line_data,40,8);
					$data[$i]["programcode1"] = substr($line_data,48,4);
					$data[$i]["programcode2"] = substr($line_data,52,4);	

					$i++;
										
				}
				
				
			}     
			//echo $i;    
			fclose($file);
			foreach ($data as $appl){
				if($appl["status"]=="Saved"){
					$txn=$transactionDB->getProfileID( $appl["applicantID"] );
					$profileID=$txn["at_appl_id"];
					$txnID=$txn["at_trans_id"];
					
					$d = substr($appl["appl_dob"],0,2);
					$m = substr($appl["appl_dob"],2,2);
					$y = substr($appl["appl_dob"],4,2);
					
					$z = (int)$y;
					if($z>12) $y="19$y"; else $y="20$y";					
					
					$profile["appl_fname"] = $appl["appl_fname"];
					$profile["appl_gender"] = $appl["appl_gender"];
					$profile["appl_religion"] = $appl["appl_religion"];
					$profile["appl_dob"] = "$d-$m-$y"; 
					$profile["appl_nationality"] = $appl["appl_nationality"];					
					$profile["appl_phone_home"] = $appl["appl_phone_home"];
					$profile["appl_phone_hp"] = $appl["appl_phone_hp"];					
					$profile["appl_address1"] = $appl["appl_address1"];
					$profile["appl_postcode"] = $appl["appl_postcode"];	
					$profile["appl_state"] = $appl["appl_state"];
					$profile["appl_city"] = $appl["appl_city"];	
					$profile["appl_province"] = 96;
						
					$profileDB->updateData($profile,$profileID);
					
					$familyDB->deletebyprofile($profileID);
					
					$family["af_relation_type"] = 20;
					$family["af_name"] = $appl["father_af_name"];										
					$family["af_address1"] = $appl["father_af_address1"];
					$family["af_postcode"] = $appl["father_af_postcode"];
					$family["af_state"] = $appl["father_af_state"] ;
					$family["af_city"] = $appl["father_af_city"];					
					$family["af_family_condition"] = $appl["father_af_family_condition"];
					$family["af_education_level"] = $appl["father_af_education_level"]; //sis_setup_dtl
					$family["af_job"] = $appl["father_af_job"]; //tbl familyjob					
					$family["af_phone"] = $appl["father_af_phone"];					
					$family["af_appl_id"] = $profileID;
					
					if($family["af_city"]==""){
						$family["af_city"]=733; //Jakarta Pusat
					}
					
					if($family["af_state"]==""){
						$family["af_state"]=255; // DKI Jakarta
					}

					if($family["af_education_level"]==""){
						$family["af_education_level"]=0; 
					}				
					
					$familyDB->addData($family);
					
					$family2["af_relation_type"] = 21;
					$family2["af_name"] = $appl["mother_af_name"];					
					$family2["af_family_condition"] = $appl["mother_af_family_condition"];
					$family2["af_education_level"] = $appl["mother_af_education_level"]; //sis_setup_dtl
					$family2["af_job"] = $appl["mother_af_job"]; //tbl familyjob
					$family2["af_appl_id"] = $profileID;
					
					if($family2["af_education_level"]==""){
						$family2["af_education_level"]=0; 					
					}
					
					$familyDB->addData($family2);
					
					$aprogDB->delete("ap_at_trans_id = $txnID ");
					
					$prog["ap_at_trans_id"] = $txnID;
					$prog["ap_prog_code"]=$appl["programcode1"];
					$prog["ap_preference"] = 1;
					
					$aprogDB->insert($prog);
					
					if($appl["programcode2"]!=""){
						//echo $appl["programcode2"];
						$prog2["ap_at_trans_id"] = $txnID;
						$prog2["ap_prog_code"]=$appl["programcode2"];	
						$prog2["ap_preference"] = 2;				
						$aprogDB->insert($prog2);					
					}
					
					$hs["ae_institution"]=$appl["ae_institution"];
					$hs["ae_transaction_id"]=$txnID;
					$hs["ae_discipline_code"]=$appl["ae_discipline_code"];
					$hs["ae_appl_id"]=$profileID;
					
					if($hs["ae_institution"]!=""){
						$appeduDB->delete("ae_transaction_id = '$txnID'");
						$appeduDB->insert($hs); 
					}
					
					//echo "Update profile $profileID >> Update transaction $txnID<br>";
				}
			}
    	}    	
    	$this->view->data = $data;
	}
	
	public function ajaxGetLocationAction($id=null){

//	 	$storage = new Zend_Auth_Storage_Session ();
//		$data = $storage->read ();
//		if (! $data) {
//			$this->_redirect ( 'index/index' );
//		}
			
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
		exit;
    }	
}