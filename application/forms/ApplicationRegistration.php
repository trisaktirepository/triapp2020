<?php
class App_Form_ApplicationRegistration extends Zend_Form {
	
	protected $_lang;
	
	public function setLang($value) {
		$this->_lang = $value;
	}
	
	public function init(){
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','application_registration_form');;
		
		$this->addElement('radio','appl_prefer_lang', array(
			'label'=>$this->getView()->translate('prefered_language')			
		));
		
		$this->appl_prefer_lang->setMultiOptions(array('1'=>' English', '2'=>' Indonesia'));
		
		
		if ($this->_lang=="en_US"){
			$this->appl_prefer_lang->setValue("1");
		}else if ($this->_lang=="id_ID"){
			$this->appl_prefer_lang->setValue("2");
		}
		
		
		
		$this->appl_prefer_lang->setAttrib('onClick','setLanguage();'); 
		
		$this->addElement('text','appl_fname', array(
			'label'=>$this->getView()->translate('first_name'),
			'required'=>true));
		
		$this->addElement('text','appl_mname', array(
			'label'=>$this->getView()->translate('middle_name')
		));
		
		$this->addElement('text','appl_lname', array(
			'label'=>$this->getView()->translate('last_name'),
			'required'=>true));
		
		$this->addElement('text','appl_email', array(
			'label'=>$this->getView()->translate('email'),
			'required'=>true
				
		));
		
		$this->appl_email->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('NotEmpty')
		->addValidator(new Zend_Validate_EmailAddress(
				array('domain' => true)
		));
		
		$this->addElement('date','appl_dob', array(
			'label'=>$this->getView()->translate('dob'),
			'required'=>true,
			'startYear'=>date('Y')-65,
			'stopYear'=>date('Y'),
			'locale'=>$this->_lang
		));
		
		/*$this->addElement('select','appl_prefer_lang', array(
			'label'=>'Preferred Language'
		));
		
		
		$langDB = new App_Model_Language_DbTable_ApplicantLang();
		$list_lang = $langDB->getData();
		
		$this->appl_prefer_lang->addMultiOption(0,"-- Select Language --");
		foreach ($list_lang as $list){
			$this->appl_prefer_lang->addMultiOption($list['al_id'],$list['al_language']);
		}*/
		
		
		
		
		
		
		$this->addElement('radio','appl_nationality', array(
			'label'=>$this->getView()->translate('nationality'),
			'onchange'=>"changeOrigin(this); $('#appl_nationality-0').val($('#country_origin').val());",
			'required' => true	
		));
		$this->appl_nationality->setRegisterInArrayValidator(false);
				
		
		$this->appl_nationality->setMultiOptions(array('96'=>' Indonesia', '0'=>' '.$this->getView()->translate('Others')))
						->setValue("1");
		
		$this->addElement('select','country_origin', array(
			'label'=>'',
			'disabled'=>'disabled',
			'onChange' => "$('#appl_nationality-0').val($(this).val());"
		));
		
		$this->country_origin->addMultiOption(null,"Select");
		$countryDb = new App_Model_General_DbTable_Country();
		foreach ($countryDb->getData() as $list){
			$this->country_origin->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
		$captcha_element = new Zend_Form_Element_Captcha(
							    'captcha',
							    array('label' => 'Verifikasi Foto',
							        'captcha' => array(
							            'captcha' => 'Image',
							            'wordLen' => 4,
							            'timeout' => 300,
							            'font' => APPLICATION_PATH."/font/ARIAL.TTF",
							            'imgDir' => CAPCHA_PATH,
							            'imgUrl' => '/capcha/'
							        )
							    )
							);
		
		$this->addElement($captcha_element);
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>$this->getView()->translate('submit'),
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>$this->getView()->translate('cancel'),
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'index'),'default',true) . "'; return false;"
        ));
        
        $this->addElement('submit', 'clear', array(
          'label'=>'Clear',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'register'),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel', 'clear'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
	}
}
?>