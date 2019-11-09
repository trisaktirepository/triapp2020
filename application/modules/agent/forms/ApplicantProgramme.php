<?
class Agent_Form_ApplicantProgramme extends Zend_Form {
	
	protected $ae_appl_id;
	protected $admissiontype;
	protected $transactionData;
	
	public function setAe_appl_id($ae_appl_id){
		$this->ae_appl_id = $ae_appl_id;
	}
	
	public function setAdmissiontype($admissiontype){
		$this->admissiontype = $admissiontype;
	}
	
public function init(){
		
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	
		//transaction data
		$transactionDb = new App_Model_Application_DbTable_ApplicantTransaction();
		$transactionData = $transactionDb->getTransactionData($transaction_id);
		
		if( $this->admissiontype == 1 ){
			$this->initPlacementTest();
		}else
		if( $this->admissiontype == 2 ){
			$this->initHighschool();
		}else{
			
		}
	}
	
	private function initPlacementTest(){
		
		$this->setName('applicant_education');
		$this->setMethod('post');
		$this->setAttrib('id','applicant_education');
		
		$this->addElement('hidden','appl_id');
		$this->appl_id->setValue($this->ae_appl_id);
		
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		/* HIGHSCHOOL DETAILS*/
		/* START DIV */
		$this->addElement(
			'hidden',
			'div_high_school_detail',
			array(
				'required' => false,
			    'ignore' => true,
			    'autoInsertNotEmptyValidator' => false,				       
			    'decorators' => array(
			    	array(
			        	'HtmlTag', array(
				            'tag'  => 'div',
				            'id'   => 'high_school_detail',
				            'openOnly' => true,
				            'style'=>'display:block',
			            )
			       	)
			    )
			)
		);
				
		$this->div_high_school_detail->clearValidators();
				
		$this->addElement('hidden', 'high_school_title', array(
			'description' => 'High School Details',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h3>')),
		    	),
		));
		/*END START DIV*/
		
		$this->addElement('select','type', array(
			'label'=>"School Type",
			'onchange'=>'changeDiscipline(this);'
		));
		$schoolTypeDb = new App_Model_General_DbTable_SchoolType();
		$this->type->addMultiOption(null,$this->getView()->translate('please_select'));
		foreach ($schoolTypeDb->getData() as $list){
			
			if ($locale=="en_US"){
				$name =$list['st_name'];
			}else if($locale=="id_ID"){
				$name =$list['st_name_bahasa'];
			}
			$this->type->addMultiOption($list['st_id'],$name." (".$list['st_code'].")");
		}
		
		$this->addElement('select','state', array(
			'label'=>$this->getView()->translate('state_province'),
			'onChange'=>"changeSchool();"
		));
		
		$stateDb = new App_Model_General_DbTable_State();
		$this->state->addMultiOption(null,$this->getView()->translate('please_select'));

		foreach ($stateDb->getState(96) as $list){
			$this->state->addMultiOption($list['idState'],$list['StateName']);
		}
		
    	$this->addElement('select','ae_institution', array(
			'label'=>"School Name (SMA/SMK)",
    		//'onchange'=>'changeDiscipline(this);',
    		'required'=>true
		));
		$schoolMasterDb = new App_Model_General_DbTable_SchoolMaster();
		$this->ae_institution->addMultiOption(null,$this->getView()->translate('please_select'));
		/*
		foreach ($schoolMasterDb->getData() as $list){
			$this->ae_institution->addMultiOption($list['sm_id'],$list['sm_name']);
		}*/
		$this->ae_institution->setRegisterInArrayValidator(false);
		
		$this->addElement('text','ae_year_from', array(
			'label'=>"Year From",
			'required'=>true,
			'id'=>"from"
		));
		
		$this->addElement('text','ae_year_end', array(
			'label'=>"Year To",
			'required'=>true,
			'id'=>"to"
		));
		
		$this->addElement('select','ae_discpline', array(
			'label'=>"Discpline",
			'onchange'=> 'changeProgramme(this)',
			'required'=>true
		));
		$schoolDiscplineDb = new App_Model_General_DbTable_SchoolDiscipline();
		$this->ae_discpline->addMultiOption(null,$this->getView()->translate('please_select'));
		foreach ($schoolDiscplineDb->getData() as $list){
			$this->ae_discpline->addMultiOption($list['smd_code'],$list['smd_desc']);
		}
		
		/* START END DIV */
		$this->addElement(
			    'hidden',
			    'dummyy',
			    array(
			        'required' => false,
			        'ignore' => true,
			        'autoInsertNotEmptyValidator' => false,
			        'decorators' => array(
			            array(
			                'HtmlTag', array(
			                    'tag'  => 'div',
			                    'id'   => 'placement',
			                    'closeOnly' => true
			                )
			            )
			        )
			    )
		);
		$this->dummyy->clearValidators();
		/* END END DIV */
		
		$this->addElement('hidden', 'space1', array(
			'description' => '<br />',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		/* END HIGHSCHOOL DETAILS */
		
		
		/*** PROGRAM PREFERENCE ***/
		
		/* START DIV */
		$this->addElement(
			'hidden',
			'start_program_list',
			array(
				'required' => false,
			    'ignore' => true,
			    'autoInsertNotEmptyValidator' => false,				       
			    'decorators' => array(
			    	array(
			        	'HtmlTag', array(
				            'tag'  => 'div',
				            'id'   => 'program_list',
				            'openOnly' => true,
				            'style'=>'display:block',
			            )
			       	)
			    )
			)
		);
		$this->start_program_list->clearValidators();
		/* END START DIV */

		$this->addElement('hidden', 'space2', array(
			'description' => '<br />',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		
		$this->addElement('hidden', 'title_program_list', array(
			'description' => '<h3>'.$this->getView()->translate('Prefered Programme').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		
		$this->addElement('select','app_id', array(
			'label'=>"Programme Preference 1",
			'required'=>true
		));
		
		
        
        $placementTestProgramList = $this->getPlacementTestProgram();
        
        $registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		if ($locale=="en_US"){
			$this->app_id->addMultiOption(null,$this->getView()->translate('please_select'));
			foreach ($placementTestProgramList as $list){
				$this->app_id->addMultiOption($list['app_id'],$list['ProgramName']." (".$list['ShortName'].")");
			}
		}else if ($locale=="id_ID"){
			$this->app_id->addMultiOption(null,$this->getView()->translate('please_select'));
			foreach ($placementTestProgramList as $list){
				$this->app_id->addMultiOption($list['app_id'],$list['ArabicName']." (".$list['ShortName'].")");
			}
		}
		
		
		$this->addElement('select','app_id2', array(
			'label'=>"Programme Preference 2"
		));
		
		if ($locale=="en_US"){
			$this->app_id2->addMultiOption(null,$this->getView()->translate('please_select'));
			foreach ($placementTestProgramList as $list){
				$this->app_id2->addMultiOption($list['app_id'],$list['ProgramName']." (".$list['ShortName'].")");
			}
		}else if ($locale=="id_ID"){
			$this->app_id2->addMultiOption(null,$this->getView()->translate('please_select'));
			foreach ($placementTestProgramList as $list){
				$this->app_id2->addMultiOption($list['app_id'],$list['ArabicName']." (".$list['ShortName'].")");
			}
		}
		
		/* START END DIV */
		$this->addElement(
			    'hidden',
			    'end_program_list',
			    array(
			        'required' => false,
			        'ignore' => true,
			        'autoInsertNotEmptyValidator' => false,
			        'decorators' => array(
			            array(
			                'HtmlTag', array(
			                    'tag'  => 'div',
			                    'id'   => 'program',
			                    'closeOnly' => true
			                )
			            )
			        )
			    )
		);
		$this->end_program_list->clearValidators();
		/* END END DIV*/
		
		/* END PROGRAMME PREFERENCE */
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>$this->getView()->translate('Save & Next'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
        ));
	}
	
	private function initHighschool(){
		
		$this->setName('applicant_education');
		$this->setMethod('post');
		$this->setAttrib('id','applicant_education');
		
		$this->addElement('hidden','appl_id');
		$this->appl_id->setValue($this->ae_appl_id);
		
		/* START DIV */
		$this->addElement(
			'hidden',
			'div_high_school_detail',
			array(
				'required' => false,
			    'ignore' => true,
			    'autoInsertNotEmptyValidator' => false,				       
			    'decorators' => array(
			    	array(
			        	'HtmlTag', array(
				            'tag'  => 'div',
				            'id'   => 'high_school_detail',
				            'openOnly' => true,
				            'style'=>'display:block',
			            )
			       	)
			    )
			)
		);
				
		$this->div_high_school_detail->clearValidators();
				
		$this->addElement('hidden', 'high_school_title', array(
			'description' => 'High School Details',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h3>')),
		    	),
		));
		/*END START DIV*/
		
		$this->addElement('select','type', array(
			'label'=>"School Type",
			'onchange'=>'changeDiscipline(this);'
		));
		$schoolTypeDb = new App_Model_General_DbTable_SchoolType();
		$this->type->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($schoolTypeDb->getData() as $list){
			$this->type->addMultiOption($list['st_id'],$list['st_name']." (".$list['st_code'].")");
		}
		
		$this->addElement('select','state', array(
			'label'=>$this->getView()->translate('state_province'),
			'onChange'=>"changeSchool();"
		));
		
		$stateDb = new App_Model_General_DbTable_State();
		$this->state->addMultiOption(0,$this->getView()->translate('please_select'));

		foreach ($stateDb->getState(96) as $list){
			$this->state->addMultiOption($list['idState'],$list['StateName']);
		}
		
    	$this->addElement('select','ae_institution', array(
			'label'=>"School Name (SMA/SMK)",
    		//'onchange'=>'changeDiscipline(this);',
    		'required'=>true
		));
		$schoolMasterDb = new App_Model_General_DbTable_SchoolMaster();
		$this->ae_institution->addMultiOption(0,$this->getView()->translate('please_select'));
		/*foreach ($schoolMasterDb->getData() as $list){
			$this->ae_institution->addMultiOption($list['sm_id'],$list['sm_name']);
		}*/
		$this->ae_institution->setRegisterInArrayValidator(false);
		
		$this->addElement('text','ae_year_from', array(
			'label'=>"Year From",
			'required'=>true,
			'id'=>"from"
		));
		
		$this->addElement('text','ae_year_end', array(
			'label'=>"Year To",
			'required'=>true,
			'id'=>"to"
		));
		
		$this->addElement('select','ae_discpline', array(
			'label'=>"Discpline",
			'onchange'=>'validateSchool(this);',
			'required'=>true
		));
		$schoolDiscplineDb = new App_Model_General_DbTable_SchoolDiscipline();
		$this->ae_discpline->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($schoolDiscplineDb->getData() as $list){
			$this->ae_discpline->addMultiOption($list['smd_code'],$list['smd_desc']);
		}
		
		/* START END DIV */
		$this->addElement(
			    'hidden',
			    'dummyy',
			    array(
			        'required' => false,
			        'ignore' => true,
			        'autoInsertNotEmptyValidator' => false,
			        'decorators' => array(
			            array(
			                'HtmlTag', array(
			                    'tag'  => 'div',
			                    'id'   => 'placement',
			                    'closeOnly' => true
			                )
			            )
			        )
			    )
		);
		$this->dummyy->clearValidators();
		/* END END DIV */
		
		$this->addElement('hidden', 'space1', array(
			'description' => '<br />',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		
		/*** SUBJECT LIST ***/
		
		/* START DIV */
		$this->addElement(
			'hidden',
			'start_subject_list',
			array(
				'required' => false,
			    'ignore' => true,
			    'autoInsertNotEmptyValidator' => false,				       
			    'decorators' => array(
			    	array(
			        	'HtmlTag', array(
				            'tag'  => 'div',
				            'id'   => 'subject_list',
				            'openOnly' => true,
				            'style'=>'display:none',
			            )
			       	)
			    )
			)
		);
		$this->start_subject_list->clearValidators();
		/* END START DIV */
				
		$this->addElement('hidden', 'test', array(
			'description' => '<h3>'.$this->getView()->translate('Subject List').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		
		
		
		/* START END DIV */
		$this->addElement(
			    'hidden',
			    'end_subject_list',
			    array(
			        'required' => false,
			        'ignore' => true,
			        'autoInsertNotEmptyValidator' => false,
			        'decorators' => array(
			            array(
			                'HtmlTag', array(
			                    'tag'  => 'div',
			                    'id'   => 'placement',
			                    'closeOnly' => true
			                )
			            )
			        )
			    )
		);
		$this->end_subject_list->clearValidators();
		/* END END DIV*/
		
		
		/*** PROGRAM PREFERENCE ***/
		
		/* START DIV */
		$this->addElement(
			'hidden',
			'start_program_list',
			array(
				'required' => false,
			    'ignore' => true,
			    'autoInsertNotEmptyValidator' => false,				       
			    'decorators' => array(
			    	array(
			        	'HtmlTag', array(
				            'tag'  => 'div',
				            'id'   => 'program_list',
				            'openOnly' => true,
				            'style'=>'display:block',
			            )
			       	)
			    )
			)
		);
		$this->start_program_list->clearValidators();
		/* END START DIV */

		$this->addElement('hidden', 'space2', array(
			'description' => '<br />',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		
		$this->addElement('hidden', 'title_program_list', array(
			'description' => '<h3>'.$this->getView()->translate('Prefered Programme').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'')),
		    	),
		));
		
		$this->addElement('select','ap_prog_code', array(
			'label'=>"Programme Preference 1",
			'required'=>true
		));
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		if ($locale=="en_US"){
			$this->ap_prog_code->addMultiOption(null,$this->getView()->translate('please_select'));
			if($this->getHighschoolProgram()){
				foreach ($this->getHighschoolProgram() as $list){
					$this->ap_prog_code->addMultiOption($list['apr_program_code'],$list['ProgramName']." (".$list['ShortName'].")");
				}
			}
		}else if ($locale=="id_ID"){
			$this->ap_prog_code->addMultiOption(null,$this->getView()->translate('please_select'));
			if($this->getHighschoolProgram()){
								
				foreach ($this->getHighschoolProgram() as $list){
					$this->ap_prog_code->addMultiOption($list['apr_program_code'],$list['ArabicName']." (".$list['ShortName'].")");
				}
			}
		}
		
		/*$this->addElement('select','ap_prog_code2', array(
			'label'=>"Programme Preference 2"
		));
		if ($locale=="en_US"){
			$this->ap_prog_code2->addMultiOption(0,$this->getView()->translate('please_select'));
			if($this->getHighschoolProgram()){
				foreach ($this->getHighschoolProgram() as $list){
					$this->ap_prog_code2->addMultiOption($list['apr_program_code'],$list['ProgramName']." (".$list['ShortName'].")");
				}
			}
		}else if ($locale=="id_ID"){
			$this->ap_prog_code2->addMultiOption(0,$this->getView()->translate('please_select'));
			if($this->getHighschoolProgram()){
				foreach ($this->getHighschoolProgram() as $list){
					$this->ap_prog_code2->addMultiOption($list['apr_program_code'],$list['ArabicName']." (".$list['ShortName'].")");
				}
			}
		}*/
		
		/* START END DIV */
		$this->addElement(
			    'hidden',
			    'end_program_list',
			    array(
			        'required' => false,
			        'ignore' => true,
			        'autoInsertNotEmptyValidator' => false,
			        'decorators' => array(
			            array(
			                'HtmlTag', array(
			                    'tag'  => 'div',
			                    'id'   => 'program',
			                    'closeOnly' => true
			                )
			            )
			        )
			    )
		);
		$this->end_program_list->clearValidators();
		/* END END DIV*/
		
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>$this->getView()->translate('Save & Next'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
        ));
	}
	
	private function getPlacementTestProgram(){
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//get placement test data
		$select = $db->select(array('apt_ptest_code'))
	                 ->from(array('ap'=>'applicant_ptest'))
	                 ->where('ap.apt_at_trans_id = ?', $transaction_id);
	                 
	    $stmt = $db->query($select);
        $placementTestCode = $stmt->fetch();
        
        if($placementTestCode){
	        //get placementest program data
		  	$select = $db->select()
		                 ->from(array('app'=>'appl_placement_program'))
		                 ->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code' )
		                 ->where('app.app_placement_code  = ?', $placementTestCode['apt_ptest_code'])
		                 ->order('p.ArabicName ASC');
	
		  	// check program offer
		  	$select->where("p.UsmOffer = 1");
		  	
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if($row){
	        	return $row;
	        }else{
	        	return null;
	        }
        }else{
        	return null;
        }
	}
	
	private function getHighschoolProgram($displine_code=null, $academic_year=null){
		$auth = Zend_Auth::getInstance();    	
    	$transaction_id = $auth->getIdentity()->transaction_id;
    	$appl_id = $this->ae_appl_id;
    	
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
	                 
	    if($displine_code){
	    	$select->where('apr.apr_decipline_code  = ?', $displine_code);
	    }
	    
		if($academic_year){
	    	$select->where('apr.apr_academic_year  = ?', $academic_year);
	    }

	    // check program offer
	    $select->where("p.PssbOffer = 1");
	 
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
        
	}
}
?>