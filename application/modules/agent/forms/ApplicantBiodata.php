<?php 
class Agent_Form_ApplicantBiodata extends Zend_Form {

protected $_lang;
	
	public function setLang($value) {
		$this->_lang = $value;
	}
	
	public function init(){
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','biodata_form');;

		
		
		$this->addElement('hidden','appl_id', array(
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));


		$this->addElement('hidden', 'test', array(
		'description' => '<h3>'.$this->getView()->translate('personal_info').'</h3>',
		'ignore' => true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'4')),
		array('Description', array('escape'=>false, 'tag'=>'')),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));

		$this->addElement('text','appl_fname', array(
		'label'=>'First Name',
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));



		$this->addElement('text','appl_mname', array(
			'label'=>'Middle Name',
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
				array('Label', array('tag' => 'td' )),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
			),
		));




		$this->addElement('text','appl_lname', array(
		'label'=>'Last Name',
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));



		$this->addElement('text','appl_email', array(
		'label'=>'email'.' *',
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));




		$this->addElement('date','appl_dob', array(
		'label'=>$this->getView()->translate('dob').' *',
		'required'=>true,
		'startYear'=>date('Y')-38,
		'stopYear'=>date('Y'),
		'locale'=>$this->_lang,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));


		$this->addElement('text','appl_birth_place', array(
		'label'=>'Place of Birth',		
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));
		

		$this->addElement('radio','appl_gender', array(
		'label'=>$this->getView()->translate('Gender').' *',
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));

		$this->appl_gender->setMultiOptions(array('1'=>' '.$this->getView()->translate('Male'), '2'=>' '.$this->getView()->translate('Female')))
		->setValue("")
		->setSeparator('&nbsp;');



		$this->addElement('radio','appl_nationality', array(
		'label'=>$this->getView()->translate('nationality').' *',
		'required'=>true,
		'onchange'=>"changeOrigin(this, $('#appl_state'),$('#mappl_state'));",
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));


		$this->appl_nationality->setMultiOptions(array('1'=>' Indonesia', '0'=>' '.$this->getView()->translate('Others')))
		->setValue("")->setSeparator('&nbsp;');



		$this->addElement('select','country_origin', array(
		'label'=>'',
		'disabled'=>'disabled',
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));

		$this->country_origin->addMultiOption(null,$this->getView()->translate('Please Select'));
		$countryDb = new App_Model_General_DbTable_Country();
		foreach ($countryDb->getData() as $list){
			$this->country_origin->addMultiOption($list['idCountry'],$list['CountryName']);
		}



		//admission type		
		/*$this->addElement('radio','appl_admission_type', array(
		'label'=>'Admission Type',
		'onchange'=>"showdiv(this);",
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));
		
		$this->appl_admission_type->setMultiOptions(array('1'=>' '.$this->getView()->translate('Placement Test'),'2'=>' '.$this->getView()->translate('High School Certificate')))->setRequired(true);
		$this->appl_admission_type->setValue(1);*/
		
	
		//religion
		$this->addElement('select','appl_religion', array(
			'label'=>'Religion',
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
				array('Label', array('tag' => 'td' )),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
			),
		));


	
		$setupDB = new App_Model_General_DbTable_Setup();
		$list_religion = $setupDB->getData('RELIGION');
		$this->appl_religion->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_religion as $religion){
			if($this->_lang=="id_ID"){
				$religion_name = $religion['ssd_name_bahasa'];
			}else if($this->_lang=="en_US"){
				$religion_name = $religion['ssd_name'];
			}
			$this->appl_religion->addMultiOption($religion['ssd_id'],$religion_name);
		}


		//Marital Status
		$this->addElement('select','appl_marital_status', array(
			'label'=>$this->getView()->translate('Marital Status'),
			'onchange'=>"enableChildren(this);",
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
				array('Label', array('tag' => 'td' )),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
			),
		));

		$list_marital = $setupDB->getData('MARITAL');
		
		$this->appl_marital_status->addMultiOption(0,$this->getView()->translate('Please Select'));
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		if ($locale=="en_US"){
			foreach ($list_marital as $marital){
				$this->appl_marital_status->addMultiOption($marital['ssd_id'],$marital['ssd_name']);
			}
		}else if ($locale=="id_ID"){
			foreach ($list_marital as $marital){
				$this->appl_marital_status->addMultiOption($marital['ssd_id'],$marital['ssd_name_bahasa']);
			}
		}
			

		$this->addElement('text','appl_no_of_child', array(
			'label'=>$this->getView()->translate('No of Children'),
			'disabled'=>'disabled',
			'maxlength'=>'2',
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
				array('Label', array('tag' => 'td' )),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
			),
		));
		
		//$this->appl_no_of_child->setValue('0');




		//Jacket Size
		$this->addElement('select','appl_jacket_size', array(
			'label'=>$this->getView()->translate('Jacket Size'),
			'decorators'=>array(
					'ViewHelper',
					'Description',
					'Errors',
					array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
					array('Label', array('tag' => 'td' )),
					array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
			),
		));

		$list_size = $setupDB->getData('JS');

		$this->appl_jacket_size->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_size as $size){
			$this->appl_jacket_size->addMultiOption($size['ssd_id'],$size['ssd_name']);
		}


		$this->addElement('hidden', 'correspondance', array(
			'description' => '<br /><h3>'.$this->getView()->translate('parents_info').'</h3>',
			'ignore' => true,
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'4')),
				array('Description', array('escape'=>false, 'tag'=>'')),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
			),
		));

		
		
		$this->addElement('text','father_name', array(
		'label'=>$this->getView()->translate("Father's Name").' *',
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		
		$this->addElement('text','mother_name', array(
		'label'=>$this->getView()->translate("Mother's Name").' ',
		'required'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('emptyrow'=>'HtmlTag'), array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'tag'=>'td', 'rowspan'=>'13' , 'width' => '50px')),
               
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		$this->addElement('hidden','relationtype', array(
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		$this->relationtype->setValue('20');

		

		$this->addElement('hidden','mrelationtype', array(
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		$this->mrelationtype->setValue('21');
		
		$this->addElement('checkbox','check_opt', array(
		'label'=>$this->getView()->translate('same_as_father_address?'),
		'onclick'=>"filladdress(this);",
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td')),
		array('HtmlTag', array('tag' => 'td' , 'colspan' => '2' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		),
		));
		
		
		$this->addElement('text','appl_address1', array(
		'label'=>$this->getView()->translate('address_1'),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		
		
		$this->addElement('text','mappl_address1', array(
		'label'=>$this->getView()->translate('address_1'),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		
		$this->addElement('text','appl_address2', array(
		'label'=>$this->getView()->translate('address_2'),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		
		$this->addElement('text','mappl_address2', array(
		'label'=>$this->getView()->translate('address_2'),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		
		$this->addElement('select','appl_state', array(
		'label'=>$this->getView()->translate('state_province'),
		'onChange'=>"changeCity(this, $('#appl_city'));",
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		$stateDb = new App_Model_General_DbTable_State();
		$this->appl_state->addMultiOption(0,$this->getView()->translate('please_select'));

		foreach ($stateDb->getState(96) as $list){
			$this->appl_state->addMultiOption($list['idState'],$list['StateName']);
		}
		
		
		$this->addElement('select','mappl_state', array(
		'label'=>$this->getView()->translate('state_province'),
		'onChange'=>"changeCity(this, $('#mappl_city'));",
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		
		$stateDb = new App_Model_General_DbTable_State();
		$this->mappl_state->addMultiOption(0,$this->getView()->translate('please_select'));

		foreach ($stateDb->getState(96) as $list){
			$this->mappl_state->addMultiOption($list['idState'],$list['StateName']);
		}
		
		
		$this->addElement('select','appl_city', array(
		'label'=>$this->getView()->translate('city'),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		$cityDb = new App_Model_General_DbTable_City();
		$this->appl_city->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->appl_city->addMultiOption($list['idCity'],$list['CityName']);
		}
		
		
		$this->addElement('select','mappl_city', array(
		'label'=>$this->getView()->translate('city'),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		$cityDb = new App_Model_General_DbTable_City();
		$this->mappl_city->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->mappl_city->addMultiOption($list['idCity'],$list['CityName']);
		}
		
		
		$this->addElement('text','appl_postcode', array(
		'label'=>$this->getView()->translate('postcode'),
		'maxlength'=>'5',
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		
		$this->addElement('text','mappl_postcode', array(
		'label'=>$this->getView()->translate('postcode'),
		'maxlength'=>'5',
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		
		$this->addElement('text','telephone', array(
		'label'=>$this->getView()->translate("Telephone"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		
		$this->addElement('text','mtelephone', array(
		'label'=>$this->getView()->translate("Telephone"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		
		$this->addElement('text','email', array(
		'label'=>$this->getView()->translate("email"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		
		$this->addElement('text','memail', array(
		'label'=>$this->getView()->translate("email"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		//family job
		$this->addElement('select','job', array(
		'label'=>$this->getView()->translate("Job"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));
		
		$familyJobDb = new App_Model_General_DbTable_FamilyJob();
		$jobList = $familyJobDb->getData();
		$this->job->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($jobList as $edulevel){
			$this->job->addMultiOption($edulevel['afj_id'],$edulevel['afj_title']);
		}
		
		//family job
		$this->addElement('select','mjob', array(
		'label'=>$this->getView()->translate("Job"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));
		
		$familyJobDb = new App_Model_General_DbTable_FamilyJob();
		$jobList = $familyJobDb->getData();
		$this->mjob->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($jobList as $edulevel){
			/*if($this->_lang=="id_ID"){
				$job = $edulevel['afj_title_bahasa'];
			}else if($this->_lang=="en_US"){
				
			}*/
			$job = $edulevel['afj_title'];
			$this->mjob->addMultiOption($edulevel['afj_id'],$job);
		}
		
		//education level
		$this->addElement('select','edulevel', array(
		'label'=>$this->getView()->translate("Education Level"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));

		$setupDB = new App_Model_General_DbTable_Setup();
		$list_edulevel = $setupDB->getData('EDULEVEL');


		$this->edulevel->addMultiOption(0,$this->getView()->translate('Please Select'));

		if ($locale=="en_US"){
			foreach ($list_edulevel as $edulevel){
				$this->edulevel->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name']);
			}
		}else if ($locale=="id_ID"){
			foreach ($list_edulevel as $edulevel){
				$this->edulevel->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name_bahasa']);
			}			
		}
		
		
		//education level
		$this->addElement('select','medulevel', array(
		'label'=>$this->getView()->translate("Education Level"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));

		$setupDB = new App_Model_General_DbTable_Setup();
		$list_edulevel = $setupDB->getData('EDULEVEL');


		$this->medulevel->addMultiOption(0,$this->getView()->translate('Please Select'));
		if ($locale=="en_US"){
			foreach ($list_edulevel as $edulevel){
				$this->medulevel->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name']);
			}
		}else if ($locale=="id_ID"){
			foreach ($list_edulevel as $edulevel){
				$this->medulevel->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name_bahasa']);
			}			
		}		

		
		//parent condition
		$this->addElement('select','condition', array(
		'label'=>$this->getView()->translate("Father's Condition"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
		),
		));

		$setupDB = new App_Model_General_DbTable_Setup();
		$list_con = $setupDB->getData('PARENTCON');


		if ($locale=="en_US"){
			foreach ($list_con as $edulevel){
				$this->condition->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name']);
			}	
		}else if ($locale=="id_ID"){
			foreach ($list_con as $edulevel){
				$this->condition->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name_bahasa']);
			}
		}
		
		
		//parent condition
		$this->addElement('select','mcondition', array(
		'label'=>$this->getView()->translate("Mother's Condition"),
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
		),
		));

		$setupDB = new App_Model_General_DbTable_Setup();
		$list_con = $setupDB->getData('PARENTCON');


		$this->mcondition->addMultiOption(0,$this->getView()->translate('Please Select'));
		
		if ($locale=="en_US"){
			foreach ($list_con as $edulevel){
				$this->mcondition->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name']);
			}	
		}else if ($locale=="id_ID"){
			foreach ($list_con as $edulevel){
				$this->mcondition->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name_bahasa']);
			}
		}
		
		

		//button
		$this->addElement('submit', 'save', array(
		'label'=>'submit',
		'decorators'=>array(
	          'ViewHelper',
               'Description',
               'Errors', 
               array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'5',
               'align'=>'left', 'openOnly'=>true)),
               array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'openOnly'=>true))
               )
		));

		$this->addElement('submit', 'cancel', array(
		'label'=>'cancel',
		'decorators'=>array(
	          'ViewHelper',
               'Description',
               'Errors', 
               array(array('data'=>'HtmlTag'), array('tag' => 'td' ,
               'closeOnly'=>true)),
               array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'closeOnly'=>true))
               ),
		'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'agent', 'controller'=>'index','action'=>'biodata'),'default',true) . "'; return false;"
		));


		$this->setDecorators(array(

		'FormElements',

		array(array('data'=>'HtmlTag'),array('tag'=>'table')),

		'Form'



		));





	}
}
?>