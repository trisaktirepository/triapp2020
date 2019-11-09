<?
class Agent_Form_ApplicantContactInfo extends Zend_Form {
	
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
		
		$this->addElement('hidden', 'indication', array(
		    'description' =>$this->getView()->translate('indicate_compulsory'),
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		$this->addElement('checkbox','check_opt_same', array(
			'label'=>$this->getView()->translate('same_as_mother_address?'),		
			'onclick'=>"fillmotheraddress(this);"		
		));				
		

		$this->addElement('text','appl_address1', array(
			'label'=>$this->getView()->translate('address_1').'*',
			'required'=>true));
		
		$this->addElement('text','appl_address2', array(
			'label'=>$this->getView()->translate('address_2').'*',
			'required'=>true));		
		
				
		$this->addElement('select','appl_province', array(
			'label'=>$this->getView()->translate('country').'*',
		    'onChange'=>"changeState(this, $('#appl_state'));"
		));	

		$countryDb = new App_Model_General_DbTable_Country();
		$this->appl_province->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($countryDb->getData() as $list){
			$this->appl_province->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
	    $this->addElement('select','appl_state', array(
			'label'=>$this->getView()->translate('state_province').'*',
	    	'onChange'=>"changeCity(this, $('#appl_city'));"
		));	
		$stateDb = new App_Model_General_DbTable_State();
		$this->appl_state->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($stateDb->getData() as $list){
			$this->appl_state->addMultiOption($list['idState'],$list['StateName']);
		}

		$this->addElement('select','appl_city', array(
			'label'=>$this->getView()->translate('city').'*'
		));	
		$cityDb = new App_Model_General_DbTable_City();
		$this->appl_city->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->appl_city->addMultiOption($list['idCity'],$list['CityName']);
		}
			
		
		$this->addElement('text','appl_postcode', array(
			'label'=>$this->getView()->translate('postcode').'*',
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
			'label'=>$this->getView()->translate('state_province'),
	    	'onChange'=>"changeCity(this, $('#appl_ccity'));"
		));	
		$stateDb = new App_Model_General_DbTable_State();
		$this->appl_cstate->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($stateDb->getData() as $list){
			$this->appl_cstate->addMultiOption($list['idState'],$list['StateName']);
		}

		$this->addElement('select','appl_ccity', array(
			'label'=>$this->getView()->translate('city')
		));
		$this->appl_ccity->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->appl_ccity->addMultiOption($list['idCity'],$list['CityName']);
		}
			
		
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
		$this->appl_phone_hp->addValidator('digits', true);
		
		$this->addElement('text','appl_phone_home', array(
			'label'=>$this->getView()->translate('home_phone'),
			'required'=>true));
		$this->appl_phone_home->addValidator('digits', true);
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'agent', 'controller'=>'index','action'=>'biodata'),'default',true) . "'; return false;"
        ));
        
      
	}
}
?>