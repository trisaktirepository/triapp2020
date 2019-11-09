<?php 

class SkpiController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
    	 
    	 
    	$this->view->title = "Surat Keterangan Pendamping Ijazah";
    	 

    }
    
    
    
    public function organisasiAction()
    {
    
    	$this->view->title = "";
    	
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	
    	//print_r($auth->getIdentity());
    	
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    	 
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	$dbOrg=new App_Model_Skpi_DbTable_Organisasi();
    	$organisasis=$dbOrg->getDatabyStudent($registration_id);
    	 
    	$dbupload=new App_Model_Skpi_DbTable_UploadFile();
    	foreach ($organisasis as $key=>$value) {
    		$idhonor=$value['idOrganisasi'];
    		$files=$dbupload->getFileItems($registration_id,$idhonor, '102');
    		$path=$files['pathupload'];
    		$organisasis[$key]['path']=$path;
    	}
    	$this->view->organisasis = $organisasis;
    	$this->view->level_list = $dbOrg->fnGetLevelHonors();
    	$this->view->occupacy_list = $dbOrg->fnGetOccupacy();
    	$this->view->category_list = $dbOrg->fnGetCategory();
    	$this->view->year_list = $dbOrg->fnGetyear();
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    		$formData = $this->getRequest()->getPost();
    		//echo var_dump($formData);exit;
    		$this->view->idOrganisasi = $formData['idOrganisasi'];
    		$formData['id_user']= $registration_id;;
    		$formData['idStudentRegistration']= $registration_id;
    		//echo var_dump($formData);exit;
    		if (isset($formData['idOrganisasi']) && $formData['idOrganisasi']!='') {
    	
    			$dbOrg->updateData($formData, $formData['idOrganisasi']);
    		}
    		else {
    	
    			$dbOrg->addData($formData);
    		}
    	
    		$this->_redirect("/default/skpi#Tab2");
    	
    	
    	}
    	 
    }
    public function languangeAction()
    {
    	$this->view->title = "";
    	
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    	
    	//print_r($auth->getIdentity());
    	
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    	 
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	  
    	 
    }
     
    public function honorsAction(){
    
    	$this->view->title = "";
    
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
    
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	//print_r($auth->getIdentity());
    
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    	 
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	$dbHonors=new App_Model_Skpi_DbTable_Honors();
    	$honors=$dbHonors->getDatabyStudent($registration_id);
    	
    	$dbupload=new App_Model_Skpi_DbTable_UploadFile();
    	foreach ($honors as $key=>$value) {
    		$idhonor=$value['idHonors'];
    		$files=$dbupload->getFileItems($registration_id,$idhonor, '101');
    		$path=$files['pathupload'];
    		$honors[$key]['path']=$path;
    	}
    	$this->view->honors = $honors;
    	$this->view->level_list = $dbHonors->fnGetLevelHonors();
    	$this->view->field_list = $dbHonors->fnGetFieldsHonors();
    	
    	
    	if ($this->getRequest()->isPost()) {
    		 
    		 
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    			$this->view->idHonors = $formData['idHonor'];
    			$formData['idUser']= $registration_id;;
    			$formData['idStudentRegistration']= $registration_id;
    			//echo var_dump($formData);exit;
    			if (isset($formData['idHonor']) && $formData['idHonor']!='') {
    				
    				$dbHonors->updateData($formData, $formData['idHonor']);
    			}
    			else {
    				
    				$dbHonors->addData($formData);
    			}
    			 
    			$this->_redirect("/default/skpi#Tab1/index");
    		
    		
    	}
    	
    	 
    }
    public function honorsApprovedAction(){
    
    	
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	//print_r($auth->getIdentity());
    
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpi_DbTable_Honors();
    	 
    	$dbHonors->approvedData($registration_id, $idhonor);
    	 
    	$this->_redirect("/default/skpi#Tab1/index");
    
    
    
    	 
    
    }
    public function honorsRejectAction(){
    
    	 
    	//get applicant profile
    	$auth = Zend_Auth::getInstance();
    
    	//print_r($auth->getIdentity());
    
    	$appl_id = $auth->getIdentity()->appl_id;
    	$registration_id = $auth->getIdentity()->registration_id;
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpi_DbTable_Honors();
     
    	$dbHonors->rejectData($registration_id, $idhonor);
   
    		 
    	$this->_redirect("/default/skpi#Tab1/index");
    
    
    	
    
    
    }
    public function deleteHonorsAction(){
    
    	$this->view->title = "";
    
    	// disable layouts for this action:
    	$this->_helper->layout->disableLayout();
        	 
    	$idhonor = $this->_getParam('idhonor', 0);
    	$dbHonors=new App_Model_Skpi_DbTable_Honors();
    	 
    
        $dbHonors->deleteData($idhonor);
    		 
    	$this->_redirect("/default/skpi#Tab1/index");
    
     
    	 
    
    }
    public function uploadCertificateAction(){
    	/*
    	 * check session for transaction
    	*/
    	$auth = Zend_Auth::getInstance();
    	 
    	 
    	$auth = Zend_Auth::getInstance();
    	$appl_id = $auth->getIdentity()->appl_id;
    	$this->view->appl_id = $appl_id;
    	 
    	$Idregistration = $auth->getIdentity()->registration_id;
    	$this->view->registration_id = $Idregistration;
     
    	if ($this->getRequest ()->isPost ()) {
    		
    		$formData = $_POST;
    		$docname=$formData['document_name'];
    		$uploadfileDB = new App_Model_Skpi_DbTable_UploadFile();
    		$idhonor=$formData['items_id'];
    		$redirect=$formData['redirect_path'];
    		if ($docname=='Honors') $docact='101';
    		else if ($docname=='Honors') $docact='101';
    		else if ($docname=='Organisasi') $docact='102';
    		else if ($docname=='Language') $docact='103';
    		else if ($docname=='Softskill') $docact='104';
    		else if ($docname=='Internship') $docact='105';
    		//$redirect=$redirect[$docname];
    		//echo var_dump($redirect);exit;
    		
    		//if($idhonors==$formData["idHonors"]){
    		///upload_file
    		
    			$apath = DOCUMENT_PATH."/student/skpi/".$Idregistration."/".$docname;
    			
    			//create directory to locate file
    			if (!is_dir($apath)) {
    				//echo($apath);exit;
    				if (!mkdir($apath, 0775,true)) echo "Can not create directory";
    			}
    			 
    			if (is_uploaded_file($_FILES["file"]['tmp_name'])){
    					
    				$ext_photo = strtolower($this->getFileExtension($_FILES["file"]["name"]));
    					
    				if($ext_photo==".pdf" || $ext_photo==".PDF" || $ext_photo==".jpg" || $ext_photo==".JPG" || $ext_photo==".jpeg" || $ext_photo==".JPEG" || $ext_photo==".gif" || $ext_photo==".GIF" || $ext_photo==".png" || $ext_photo==".PNG"){
    					$flnamephoto = date('Ymdhs')."_".$docname.$ext_photo;
    					$path_photograph = $apath."/".$flnamephoto;
    					if (move_uploaded_file($_FILES["file"]['tmp_name'], $path_photograph)) {
    
	    					$upd_photo = array(
	    							'auf_idStudentRegistration' => $Idregistration,
	    							'auf_Items'=>$idhonor,
	    							'auf_file_name' => $flnamephoto,
	    							'auf_file_type' => $docact,
	    							'auf_upload_date' => date("Y-m-d h:i:s"),
	    							'auf_upload_by' => $appl_id,
	    							'pathupload' => $path_photograph
	    					);
	    					$row=$uploadfileDB->getFileItems($Idregistration,$idhonor, $docact);
	    					if ($row)
	    						$uploadfileDB->updateData($upd_photo, $row['auf_id']);
	    					else
	    						$uploadfileDB->addData($upd_photo);
    					}
    
    				}
    					
    			}
    			 
    			//}
    			 
    		}
    		$this->_redirect($redirect);
    		
    	}
    	function getFileExtension($filename){
    		return substr($filename, strrpos($filename, '.'));
    	}
    	
    	public function organisasiApprovedAction(){
    	
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    	
    		//print_r($auth->getIdentity());
    	
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idOrg = $this->_getParam('idOrganisasi', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Organisasi();
    	
    		$dbOrg->approvedData($registration_id, $idOrg);
    	
    		$this->_redirect("/default/skpi#Tab2");
    	
    	
    	
    	
    	
    	}
    	public function organisasiRejectAction(){
    	
    	
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    	
    		//print_r($auth->getIdentity());
    	
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idOrg = $this->_getParam('idOrganisasi', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Organisasi();
    		 
    		$dbOrg->rejectData($registration_id, $idOrg);
    		 
    		 
    		$this->_redirect("/default/skpi#Tab2");
    	}
    	public function deleteOrganisasiAction(){
    	
    		$this->view->title = "";
    	
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    	
    		$idOrg = $this->_getParam('idOrganisasi', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Organisasi();
    	
    	
    		$dbOrg->deleteData($idOrg);
    		 
    		$this->_redirect("/default/skpi#Tab2");
    	
    	}
    	public function languageAction()
    	{
    	
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    	
    		$this->view->appl_id = $appl_id;
    		$this->view->IdStudentRegistration = $registration_id;
    		$dblang=new App_Model_Skpi_DbTable_Languange();
    		$languages=$dblang->getDatabyStudent($registration_id);
    	
    		$dbupload=new App_Model_Skpi_DbTable_UploadFile();
    		foreach ($languages as $key=>$value) {
    			$idhonor=$value['idLanguage'];
    			$files=$dbupload->getFileItems($registration_id,$idhonor, '103');
    			$path=$files['pathupload'];
    			$languages[$key]['path']=$path;
    		}
    		$this->view->languages = $languages;
    		$this->view->language_list = $dblang->fnGetLanguage();
    		$this->view->standart_list = $dblang->fnGetLanguageStandart();
    		 
    		if ($this->getRequest()->isPost()) {
    			 
    			 
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    			$this->view->idLanguage = $formData['idLanguage'];
    			$formData['id_user']= $registration_id;;
    			$formData['idStudentRegistration']= $registration_id;
    			//echo var_dump($formData);exit;
    			if (isset($formData['idLanguage']) && $formData['idLanguage']!='') {
    				 
    				$dblang->updateData($formData, $formData['idLanguage']);
    			}
    			else {
    				 
    				$dblang->addData($formData);
    			}
    			 
    			$this->_redirect("/default/skpi#Tab3");
    			 
    			 
    		}
    	
    	}
    	public function languageApprovedAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('idLanguage', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Languange();
    		 
    		$dbOrg->approvedData($registration_id, $idlang);
    		 
    		$this->_redirect("/default/skpi#Tab3");
    		 
    		 
    		 
    		 
    		 
    	}
    	public function languageRejectAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('idLanguage', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Languange();
    		 
    		$dbOrg->rejectData($registration_id, $idlang);
    		 
    		 
    		$this->_redirect("/default/skpi#Tab3");
    	}
    	
    	public function deleteLanguageAction(){
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		$idlang = $this->_getParam('idLanguage', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Languange();
    		 
    		 
    		$dbOrg->deleteData($idlang);
    		 
    		$this->_redirect("/default/skpi#Tab3");
    		 
    	}
    	public function softskillAction()
    	{
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		 
    		$this->view->appl_id = $appl_id;
    		$this->view->IdStudentRegistration = $registration_id;
    		$dblang=new App_Model_Skpi_DbTable_Softskill();
    		$softskills=$dblang->getDatabyStudent($registration_id);
    		 
    		$dbupload=new App_Model_Skpi_DbTable_UploadFile();
    		foreach ($softskills as $key=>$value) {
    			$idhonor=$value['idSoftskill'];
    			$files=$dbupload->getFileItems($registration_id,$idhonor, '104');
    			$path=$files['pathupload'];
    			$softskills[$key]['path']=$path;
    		}
    		$this->view->softskills = $softskills;
    		 
    		 
    		if ($this->getRequest()->isPost()) {
    	
    	
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    			$this->view->idLanguage = $formData['idSoftskill'];
    			$formData['id_user']= $registration_id;;
    			$formData['idStudentRegistration']= $registration_id;
    			//echo var_dump($formData);exit;
    			if (isset($formData['idSoftskill']) && $formData['idSoftskill']!='') {
    					
    				$dblang->updateData($formData, $formData['idSoftskill']);
    			}
    			else {
    					
    				$dblang->addData($formData);
    			}
    	
    			$this->_redirect("/default/skpi#Tab4");
    	
    	
    		}
    		 
    	}
    	
    	public function softskillApprovedAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('idSoftskill', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Softskill();
    		 
    		$dbOrg->approvedData($registration_id, $idlang);
    		 
    		$this->_redirect("/default/skpi#Tab4");
    		 
    		 
    		 
    		 
    		 
    	}
    	public function softskillRejectAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('idSoftskill', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Softskill();
    		 
    		$dbOrg->rejectData($registration_id, $idlang);
    		 
    		 
    		$this->_redirect("/default/skpi#Tab4");
    	}
    	 
    	public function deleteSoftskillAction(){
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		$idlang = $this->_getParam('idSoftskill', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Softskill();
    		 
    		 
    		$dbOrg->deleteData($idlang);
    		 
    		$this->_redirect("/default/skpi#Tab4");
    		 
    	}
    	public function internshipAction()
    	{
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		 
    		$this->view->appl_id = $appl_id;
    		$this->view->IdStudentRegistration = $registration_id;
    		$dblang=new App_Model_Skpi_DbTable_Internships();
    		$internships=$dblang->getDatabyStudent($registration_id);
    		 
    		$dbupload=new App_Model_Skpi_DbTable_UploadFile();
    		foreach ($internships as $key=>$value) {
    			$idhonor=$value['idInternship'];
    			$files=$dbupload->getFileItems($registration_id,$idhonor, '105');
    			$path=$files['pathupload'];
    			$internships[$key]['path']=$path;
    		}
    		$this->view->internships = $internships;
    		 
    		 
    		if ($this->getRequest()->isPost()) {
    			 
    			 
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    			$this->view->idLanguage = $formData['idInternship'];
    			$formData['id_user']= $registration_id;;
    			$formData['idStudentRegistration']= $registration_id;
    			//echo var_dump($formData);exit;
    			if (isset($formData['idInternship']) && $formData['idInternship']!='') {
    					
    				$dblang->updateData($formData, $formData['idInternship']);
    			}
    			else {
    					
    				$dblang->addData($formData);
    			}
    			 
    			$this->_redirect("/default/skpi#Tab5");
    			 
    			 
    		}
    		 
    	}
    	 
    	public function internshipApprovedAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('idInternship', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Internships();
    		 
    		$dbOrg->approvedData($registration_id, $idlang);
    		 
    		$this->_redirect("/default/skpi#Tab5");
    		 
    		 
    		 
    		 
    		 
    	}
    	public function internshipRejectAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('idInternship', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Internships();
    		 
    		$dbOrg->rejectData($registration_id, $idlang);
    		 
    		 
    		$this->_redirect("/default/skpi#Tab5");
    	}
    	
    	public function deleteInternshipAction(){
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		$idlang = $this->_getParam('idInternship', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Internships();
    		 
    		 
    		$dbOrg->deleteData($idlang);
    		 
    		$this->_redirect("/default/skpi#Tab5");
    		 
    	}
    	public function cpAction()
    	{
    		 
    		$this->view->title = "CP Kurikulum";
    		 
    		// disable layouts for this action:
    		//$this->_helper->layout->disableLayout();
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		$landscapeid = $this->_getParam('IdLandscape', 0);
    		$programid = $this->_getParam('IdProgram', 0);
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		$this->view->landscapeid=$landscapeid;
    		$this->view->programid=$programid;
    		$this->view->iduser = $appl_id;
    		$dblang=new App_Model_Skpi_DbTable_Cp();
    		$this->view->majoring_list=$dblang->fnGetProgramMajoring($programid);
    		
    		$cps=$dblang->getDataGrup($programid,$landscapeid,null);
    		 
    		$this->view->cps = $cps;
    		 
    		 
    		if ($this->getRequest()->isPost()) {
    	
    	
    			$formData = $this->getRequest()->getPost();
    			//echo var_dump($formData);exit;
    		 
    			$formData['id_user']= $appl_id;
    			 
    			//echo var_dump($formData);exit;
    			if (isset($formData['idSKPI']) && $formData['idSKPI']!='') {
    					
    				$dblang->updateData($formData, $formData['idSKPI']);
    			}
    			else {
    					
    				$dblang->addData($formData);
    			}
    	
    		// echo var_dump($formData);exit;
    	
    	
    		}
    		 
    	}
    	
    	public function cpApprovedAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		//$registration_id = $auth->getIdentity()->registration_id;
    		$idlang = $this->_getParam('IdSKPI', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Cp();
    		 
    		$dbOrg->approvedData($idlang);
    		 
    		$this->_redirect("/default/skpi/cp");
    		 
    		 
    		 
    		 
    		 
    	}
    	public function cpRejectAction(){
    		 
    		 
    		//get applicant profile
    		$auth = Zend_Auth::getInstance();
    		 
    		//print_r($auth->getIdentity());
    		 
    		$appl_id = $auth->getIdentity()->appl_id;
    		 
    		$idlang = $this->_getParam('IdSKPI', 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Cp();
    		 
    		$dbOrg->rejectData( $idlang);
    		 
    		 
    		$this->_redirect("/default/skpi/cp");
    	}
    	 
    	public function deleteCpAction(){
    		 
    		$this->view->title = "";
    		 
    		// disable layouts for this action:
    		$this->_helper->layout->disableLayout();
    		 
    		$idlang = $this->_getParam("IdSKPI", 0);
    		$dbOrg=new App_Model_Skpi_DbTable_Cp();
    		 
    		 
    		$dbOrg->deleteData($idlang);
    		 
    		$this->_redirect("/default/skpi/cp");
    		 
    	}
    	 
}