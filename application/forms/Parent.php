<?php
class App_Form_ContactInfo extends Zend_Form {
	
	public function init(){
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','contact_info_form');
		
		$this->addElement('hidden','appl_id');
		
		$this->addElement('hidden', 'test', array(
		    'description' => '<h3>'.$this->getView()->translate('permanent_address').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));

		$this->addElement('text','appl_address1', array(
			'label'=>$this->getView()->translate('address_1'),
			'required'=>true));
		
		$this->addElement('text','appl_address2', array(
			'label'=>$this->getView()->translate('address_2'),
			'required'=>true));		
		
				
		$this->addElement('select','appl_province', array(
			'label'=>$this->getView()->translate('country'),
		    'onChange'=>"changeState(this, $('#appl_state'));"
		));		
		
		$countryDb = new App_Model_General_DbTable_Country();
		$this->appl_province->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($countryDb->getData() as $list){
			$this->appl_province->addMultiOption($list['idCountry'],$list['CountryName']);
		}
						
						
		
	    $this->addElement('select','appl_state', array(
			'label'=>$this->getView()->translate('state_province')
		));		
			
		
		$this->addElement('text','appl_postcode', array(
			'label'=>$this->getView()->translate('postcode'),
			'required'=>true,
			'maxlength'=>'5'
		));		
		
		
		$this->addElement('hidden', 'correspondance', array(
		    'description' => '<h3>'.$this->getView()->translate('correspondance_address').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		
		$this->addElement('checkbox','check_opt', array(
			'label'=>$this->getView()->translate('same_as_permanent_address?'),		
			'onclick'=>"filladdress(this);"		
		));				
		
		
						
		
		$this->addElement('text','appl_caddress1', array(
			'label'=>$this->getView()->translate('address_1'),
			'required'=>true));
		
		$this->addElement('text','appl_caddress2', array(
			'label'=>$this->getView()->translate('address_2'),
			'required'=>true));		
		
				
		$this->addElement('select','appl_cprovince', array(
			'label'=>$this->getView()->translate('country'),
			'onChange'=>"changeState(this, $('#appl_cstate'));"
		));		
		
		$countryDb = new App_Model_General_DbTable_Country();
		$this->appl_cprovince->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($countryDb->getData() as $list){
			$this->appl_cprovince->addMultiOption($list['idCountry'],$list['CountryName']);
		}
						
						
		
	    $this->addElement('select','appl_cstate', array(
			'label'=>$this->getView()->translate('state_province')
		));		
			
		
		$this->addElement('text','appl_cpostcode', array(
			'label'=>$this->getView()->translate('postcode'),
			'required'=>true,
			'maxlength'=>'5'
		));	
		
		
		$this->addElement('hidden', 'contact', array(
		    'description' => '<h3>'.$this->getView()->translate('contact_number').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		$this->addElement('text','appl_phone_hp', array(
			'label'=>$this->getView()->translate('mobile'),
			'required'=>true));
		
		$this->addElement('text','appl_phone_home', array(
			'label'=>$this->getView()->translate('home_phone'),
			'required'=>true));
		
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
        ));
        
      
	}
}
?>