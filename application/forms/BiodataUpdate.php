<?php
class App_Form_BiodataUpdate extends Zend_Form {

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
		'disabled'=>true,
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
				'disabled'=>true,
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
				'disabled'=>true,
		//'required'=>true,
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
		'label'=>'email',
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
		
		$this->addElement('text','appl_phone_hp', array(
				'label'=>'Mobile Phone',
				//'required'=>true,
				'disabled'=>true,
				'decorators'=>array(
						'ViewHelper',
						'Description' ,
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
				),
		));
		
		$this->appl_phone_hp->setDescription("Perbaikan No HP hanya dapat dilakukan oleh admin Fakultas");
		
		$this->addElement('text','appl_phone_home', array(
				'label'=>'Home Phone',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'3')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
				),
		));


		$this->addElement('text','appl_nik', array(
				'label'=>'NIK',
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
		
		$this->addElement('hidden', 'contact', array(
				'description' => '<br /><h3>'.$this->getView()->translate('Domisili').'</h3>',
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
		
		$this->addElement('text','std_address1', array(
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
		
		 
		
		$this->addElement('text','std_address_rt', array(
				'label'=>$this->getView()->translate('RT'),
				'maxlength' => '3',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		 
		
		$this->addElement('text','std_address_rw', array(
				'label'=>$this->getView()->translate('RW'),
				'maxlength' => '3',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		 
		
		//kelurahan
		$this->addElement('text','std_kelurahan', array(
				'label'=>$this->getView()->translate('Kelurahan'),
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		 
		
		//kecamatan
		$this->addElement('text','std_kecamatan', array(
				'label'=>$this->getView()->translate('Kecamatan'),
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		 
		
			
		
		$this->addElement('select','std_province', array(
				'label'=>$this->getView()->translate('country'),
				'required'=>true,
				'onChange'=>"changeState(this, $('#std_state'));",
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				)
		));
		
		$countryDb = new App_Model_General_DbTable_Country();
		$this->std_province->addMultiOption(null,$this->getView()->translate('Please Select'));
		foreach ($countryDb->getData() as $list){
			$this->std_province->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		 
		
		$this->addElement('select','std_state', array(
				'label'=>$this->getView()->translate('state_province'),
				'onChange'=>"changeCity(this, $('#std_city'));",
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
		$this->std_state->addMultiOption(0,$this->getView()->translate('please_select'));
		
		foreach ($stateDb->getState(0) as $list){
			$this->std_state->addMultiOption($list['idState'],$list['StateName']);
		}
		
		
		 
		
		$this->addElement('select','std_city', array(
				'label'=>$this->getView()->translate('Kabupaten'),
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
		$this->std_city->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->std_city->addMultiOption($list['idCity'],$list['CityName']);
		}
		 
		
		
		//postcode
		$this->addElement('text','std_postcode', array(
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
			'label'=>$this->getView()->translate("Father's Name"),
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
		//'required'=>true,
		'disabled'=>true,
		'decorators'=>array(
		'ViewHelper',
		'Description',
		'Errors',
		array(array('data'=>'HtmlTag'), array('tag' => 'td')),
		array('Label', array('tag' => 'td' )),
		array(array('emptyrow'=>'HtmlTag'), array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'tag'=>'td', 'rowspan'=>'19' , 'width' => '50px')),
               
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
		
		
		$this->addElement('text','appl_address_rt', array(
				'label'=>$this->getView()->translate('RT'),
				'maxlength' => '3',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		$this->addElement('text','mappl_address_rt', array(
				'label'=>$this->getView()->translate('RT'),
				'maxlength' => '3',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
				),
		));
		
		$this->addElement('text','appl_address_rw', array(
				'label'=>$this->getView()->translate('RW'),
				'maxlength' => '3',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		$this->addElement('text','mappl_address_rw', array(
				'label'=>$this->getView()->translate('RW'),
				'maxlength' => '3',
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
				),
		));
		
		//kelurahan
		$this->addElement('text','appl_kelurahan', array(
				'label'=>$this->getView()->translate('Kelurahan'),
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		
		$this->addElement('text','mappl_kelurahan', array(
				'label'=>$this->getView()->translate('Kelurahan'),
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
				),
		));
		
		//kecamatan
		$this->addElement('text','appl_kecamatan', array(
				'label'=>$this->getView()->translate('Kecamatan'),
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
				),
		));
		
		
		$this->addElement('text','mappl_kecamatan', array(
				'label'=>$this->getView()->translate('Kecamatan'),
				'decorators'=>array(
						'ViewHelper',
						'Description',
						'Errors',
						array(array('data'=>'HtmlTag'), array('tag' => 'td')),
						array('Label', array('tag' => 'td' )),
						array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
				),
		));
		
		
		 
		
		$this->addElement('select','appl_province', array(
			'label'=>$this->getView()->translate('country'),
			'required'=>true,
			'onChange'=>"changeState(this, $('#appl_state'));",
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td')),
				array('Label', array('tag' => 'td' )),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr','openOnly' => true))
			)
		));
		
		$countryDb = new App_Model_General_DbTable_Country();
		$this->appl_province->addMultiOption(null,$this->getView()->translate('Please Select'));
		foreach ($countryDb->getData() as $list){
			$this->appl_province->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
		
		
		$this->addElement('select','mappl_province', array(
			'label'=>$this->getView()->translate('country'),
			'required'=>true,
			'onChange'=>"changeState(this, $('#mappl_state'));",
			'decorators'=>array(
				'ViewHelper',
				'Description',
				'Errors',
				array(array('data'=>'HtmlTag'), array('tag' => 'td')),
				array('Label', array('tag' => 'td' )),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr','closeOnly' => true))
			),
		));
		
		$countryDb = new App_Model_General_DbTable_Country();
		$this->mappl_province->addMultiOption(null,$this->getView()->translate('Please Select'));
		foreach ($countryDb->getData() as $list){
			$this->mappl_province->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
		
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

		foreach ($stateDb->getState(0) as $list){
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

		foreach ($stateDb->getState(0) as $list){
			$this->mappl_state->addMultiOption($list['idState'],$list['StateName']);
		}
		
		
		$this->addElement('select','appl_city', array(
		'label'=>$this->getView()->translate('Kabupaten'),
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
		'label'=>$this->getView()->translate('Kabupaten'),
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
		
		
		
		//postcode
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
		
		/*SPACER*/
		$this->addElement('hidden','blank', array(
				'label'=>'',
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
		$this->addElement('hidden','blank2', array(
				'label'=>'',
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
		
		
		$this->addElement('text','telephone', array(
		'label'=>$this->getView()->translate("Telephone"),
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
		$this->addElement('button', 'save', array(
		'label'=>'submit',
		'onClick'=>'onClick()',
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
		'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
		));


		$this->setDecorators(array(

		'FormElements',

		array(array('data'=>'HtmlTag'),array('tag'=>'table')),

		'Form'



		));





	}
}
?>