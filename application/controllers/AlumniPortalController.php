<?php

class AlumniPortalController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
    	
    	
    }
    
    public function biodataAction(){
    $msg = $this->_getParam('msg', null);
		if($msg){
			$this->view->noticeError = $msg;	
		}
		
		//title
    	$this->view->title = $this->view->translate("Biodata");
    	
    	$auth = Zend_Auth::getInstance();    	 
        $language = $auth->getIdentity()->appl_prefer_lang;  
       
    	$appl_id = $auth->getIdentity()->appl_id;    	
    	$this->view->appl_id = $appl_id;
    	    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	$this->view->applicant = $applicant;
    	
    	$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
    	$form = new App_Form_Biodata(array ('lang' => $locale));
    	$form->removeElement('appl_admission_type');
    	$form->removeElement('save');
    	$form->removeElement('cancel');
    	    	
    	if ($this->getRequest()->isPost()) {    		
    		
    		$formData = $this->getRequest()->getPost();  
    		
    		//check nationality
			if($formData["appl_nationality"]==96){
				$form->country_origin->setRequired(false)->setValidators(array());
			}
			
    		if ($form->isValid($formData)) {
				
    			$info["appl_fname"]=strtoupper($formData["appl_fname"]);
				$info["appl_mname"]=strtoupper($formData["appl_mname"]);
				$info["appl_lname"]=strtoupper($formData["appl_lname"]);
				$info["appl_email"]=$formData["appl_email"];			
				$info["appl_dob"]=$formData["appl_dob"]['day']."-".$formData["appl_dob"]['month']."-".$formData["appl_dob"]['year'];
				$info["appl_birth_place"]=$formData["appl_birth_place"];
				$info["appl_gender"]=$formData["appl_gender"];
				if($formData["appl_nationality"]==0){
					$formData["appl_nationality"] = $formData["country_origin"];
				}
				$info["appl_nationality"]=$formData["appl_nationality"];
				//$info["appl_admission_type"]=$formData["appl_admission_type"];
				
				$info["appl_religion"]=$formData["appl_religion"];
				$info["appl_marital_status"]=$formData["appl_marital_status"];				
				$info["appl_jacket_size"]=$formData["appl_jacket_size"];
				$info["appl_parent_salary"]=$formData["appl_parent_salary"];

				if($formData["appl_marital_status"]!=""){
					$info["appl_no_of_child"]=isset($formData["appl_no_of_child"])?$formData["appl_no_of_child"]:0;	
				}
				
                if($formData["appl_email"] != $applicant["appl_email"])
                {
                    $verifyKey = '';
                    
                    $keys = array_merge(range(0, 9), range('a', 'z'));
                    
                    for ($i = 0; $i < 20; $i++) {
                        $verifyKey .= $keys[array_rand($keys)];
                    }
                    
                    $salt = 'trisakti';
                    $beforeHash = time().$verifyKey.$salt;
                    $verifyKey = md5($beforeHash);
                    $info["verifyKey"] = $verifyKey;
                    
                }			
				
				$appProfileDB->updateData($info, $formData["appl_id"]);
				
                if(isset($info['verifyKey']))
                {
                    $this->sendemailque($appl_id);
                    
                }
                
				$father["af_appl_id"]=$formData["appl_id"];
				$father["af_name"]=$formData["father_name"];
				$father["af_relation_type"]=$formData["relationtype"];
				$father["af_address_rt"]=$formData["appl_address_rt"];
				$father["af_address_rw"]=$formData["appl_address_rw"];
				$father["af_address1"]=$formData["appl_address1"];
				//$father["af_address2"]=$formData["appl_address2"];
				$father["af_province"]=$formData["appl_province"];
				$father["af_state"]=$formData["appl_state"];
				$father["af_city"]=$formData["appl_city"];
				$father["af_kelurahan"]=$formData["appl_kelurahan"];
				$father["af_kecamatan"]=$formData["appl_kecamatan"];
				$father["af_postcode"]=$formData["appl_postcode"];
				$father["af_phone"]=$formData["telephone"];
				$father["af_email"]=$formData["email"];
				$father["af_job"]=$formData["job"];
				$father["af_education_level"]=$formData["edulevel"];
				$father["af_family_condition"]=$formData["condition"];
				
				
				
				$mother["af_appl_id"]=$formData["appl_id"];
				$mother["af_name"]=$formData["mother_name"];
				$mother["af_relation_type"]=$formData["mrelationtype"];
				$mother["af_address_rt"]=$formData["mappl_address_rt"];
				$mother["af_address_rw"]=$formData["mappl_address_rw"];
				$mother["af_address1"]=$formData["mappl_address1"];
				//$mother["af_address2"]=$formData["mappl_address2"];
				$mother["af_province"]=$formData["mappl_province"];
				$mother["af_state"]=$formData["mappl_state"];
				$mother["af_city"]=$formData["mappl_city"];
				$mother["af_kelurahan"]=$formData["mappl_kelurahan"];
				$mother["af_kecamatan"]=$formData["mappl_kecamatan"];
				$mother["af_postcode"]=$formData["mappl_postcode"];
				$mother["af_phone"]=$formData["mtelephone"];
				$mother["af_email"]=$formData["memail"];
				$mother["af_job"]=$formData["mjob"];
				$mother["af_education_level"]=$formData["medulevel"];
				$mother["af_family_condition"]=$formData["mcondition"];
				
				
				$family = new App_Model_Application_DbTable_ApplicantFamily();
				
				$rsfather=$family->fetchdata($father["af_appl_id"],$father["af_relation_type"]);
				
				if (!$rsfather){				
					$family->addData($father);
				} else {
					$family->updateData($father,$rsfather["af_id"]);
				}
				
				$rsmother=$family->fetchdata($mother["af_appl_id"],$mother["af_relation_type"]);
				
				if (!$rsmother){				
					$family->addData($mother);
				} else {
					$family->updateData($mother,$rsmother["af_id"]);
				}
	
				
				$this->view->noticeSuccess = $this->view->translate("Data Successfuly Saved");
				
				$this->view->data = $formData;
				
				$form->populate($formData);
    			$this->view->form = $form;
    			
    		} else {
    			$this->view->data = $formData;
    			
    			$form->populate($formData);
				$this->view->form = $form;
					
    		}//end isvalid form
			
    	}else{
    		$family = new App_Model_Application_DbTable_ApplicantFamily();
    		
    		$rsfather=$family->fetchdata($appl_id,20);
    		
    		if ($rsfather){
    		$applicant["father_name"]=$rsfather["af_name"];
			$applicant["relationtype"]=$rsfather["af_relation_type"];
			$applicant["appl_address_rt"]=$rsfather["af_address_rt"];
			$applicant["appl_address_rw"]=$rsfather["af_address_rw"];
			$applicant["appl_address1"]=$rsfather["af_address1"];
			$applicant["appl_address2"]=$rsfather["af_address2"];
			$applicant["appl_province"]=$rsfather["af_province"];
			$applicant["appl_state"]=$rsfather["af_state"];
			$applicant["appl_city"]=$rsfather["af_city"];
			$applicant["appl_kelurahan"]=$rsfather["af_kelurahan"];
			$applicant["appl_kecamatan"]=$rsfather["af_kecamatan"];
			$applicant["appl_postcode"]=$rsfather["af_postcode"];
			$applicant["telephone"]=$rsfather["af_phone"];
			$applicant["email"]=$rsfather["af_email"];
			$applicant["job"]=$rsfather["af_job"];
			$applicant["edulevel"]=$rsfather["af_education_level"];
			$applicant["condition"]=$rsfather["af_family_condition"];
    		}
    		
			$rsmother=$family->fetchdata($appl_id,21);
			
			if ($rsmother){
			$applicant["mother_name"]=$rsmother["af_name"];
			$applicant["mrelationtype"]=$rsmother["af_relation_type"];
			$applicant["mappl_address_rt"]=$rsmother["af_address_rt"];
			$applicant["mappl_address_rw"]=$rsmother["af_address_rw"];
			$applicant["mappl_address1"]=$rsmother["af_address1"];
			$applicant["mappl_address2"]=$rsmother["af_address2"];
			$applicant["mappl_province"]=$rsfather["af_province"];
			$applicant["mappl_state"]=$rsfather["af_state"];
			$applicant["mappl_city"]=$rsmother["af_city"];
			$applicant["mappl_kelurahan"]=$rsmother["af_kelurahan"];
			$applicant["mappl_kecamatan"]=$rsmother["af_kecamatan"];
			$applicant["mappl_postcode"]=$rsmother["af_postcode"];
			$applicant["mtelephone"]=$rsmother["af_phone"];
			$applicant["memail"]=$rsmother["af_email"];
			$applicant["mjob"]=$rsmother["af_job"];
			$applicant["medulevel"]=$rsmother["af_education_level"];
			$applicant["mcondition"]=$rsfather["af_family_condition"];
			}
				
			
    			
    		/*if($applicant['appl_admission_type']==null){
    			$applicant['appl_admission_type'] = '2';
    		}*/
			$this->view->data = $applicant;
			
	    	$form->populate($applicant);
	    	$this->view->form = $form;
    	}
    }
	
	public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        
        $this->_redirect($this->view->url(array('module'=>'default','controller'=>'online-application', 'action'=>'index'),'default',true));
    }
    
    
    
    
    public function changePasswordAction()
    {
        $this->view->title = $this->view->translate('Change Password');
        
        $auth = Zend_Auth::getInstance();    	
        
    	$appl_id = $auth->getIdentity()->appl_id; 
    	
    	$appProfileDB  = new App_Model_Application_DbTable_ApplicantProfile();	
    	$applicant = $appProfileDB->getData($appl_id);
    	
        $this->view->applicant = $applicant;
        $this->view->incorrect = false;
        $this->view->msg       = false;
        
        if($this->getRequest()->isPost())
        {
            $formData = $this->getRequest()->getPost();
            //print_r($formData);
            if($formData['current_password'] == $applicant['appl_password'])
            {
                $saveData = array('appl_password' => $formData['new_password']);
                $appProfileDB->updateData($saveData,$appl_id);
                $this->view->msg       = true;
            }
            else
            {
                $this->view->incorrect = true;
            }
        }
        
    }
    
    
    public function homeAction(){
    	
    	$this->view->title = $this->view->translate('Home');
        
        
    }
    
    public function resultAction(){
    	
    	$this->view->title = "Examination Result";
    	
      }
}

?>