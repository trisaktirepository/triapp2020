<?php

class GeneralSetup_Form_SchoolMaster extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','form_sm');
		
		$this->addElement('hidden', 'title1', array(
			'description' => 'Basic Details',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h3>')),
		    	),
		));
		
		$this->addElement('text','sm_name', 
			array(
				'label'=>'School Name',
				'required'=>'true'
			)
		);
		
		$this->addElement('text','sm_name_bahasa', 
			array(
				'label'=>'School Name (Indonesia)'
			)
		);
		
		$this->addElement('text','sm_school_code', 
			array(
				'label'=>'Code'
			)
		);
		
		$this->addElement('select','sm_type', 
			array(
				'label'=>'School Type',
				'required'=>'true'
			)
		);
		$schoolTypeDb = new App_Model_General_DbTable_SchoolType();
		$listData = $schoolTypeDb->getData();
		$this->sm_type->addMultiOption(null,"Please Select");
		foreach ($listData as $list){
			$this->sm_type->addMultiOption($list['st_id'],$list['st_name']." (".$list['st_code'].")");
		}

		$this->addElement('hidden', 'title2', array(
			'description' => 'Contact Details',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h3>')),
		    	),
		));
		
		$this->addElement('text','sm_address1', 
			array(
				'label'=>'Address',
				'required'=>'true'
			)
		);
		
		$this->addElement('text','sm_address2', 
			array(
				'label'=>'',
			)
		);
		
		
		$this->addElement('select','sm_country', array(
			'label'=>$this->getView()->translate('country'),
			'required'=>'true',
		    'onChange'=>"changeState(this, $('#sm_state'));"
		));	

		$countryDb = new App_Model_General_DbTable_Country();
		$this->sm_country->addMultiOption(null,$this->getView()->translate('please_select'));
		foreach ($countryDb->getData() as $list){
			$this->sm_country->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
	    $this->addElement('select','sm_state', array(
			'label'=>$this->getView()->translate('state_province'),
	    	'required'=>'true',
	    	'onChange'=>"changeCity(this, $('#sm_city'));"
		));	
		$stateDb = new App_Model_General_DbTable_State();
		$this->sm_state->addMultiOption(null,$this->getView()->translate('please_select'));
		foreach ($stateDb->getData() as $list){
			$this->sm_state->addMultiOption($list['idState'],$list['StateName']);
		}

		$this->addElement('select','sm_city', array(
			'label'=>$this->getView()->translate('city'),
			'required'=>'true'
		));	
		$cityDb = new App_Model_General_DbTable_City();
		$this->sm_city->addMultiOption(null,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->sm_city->addMultiOption($list['idCity'],$list['CityName']);
		}
		
		
		$this->addElement('text','sm_email', 
			array(
				'label'=>'Email'
			)
		);
		$this->sm_email->addValidator('emailAddress', true);
		
		$this->addElement('text','sm_url', 
			array(
				'label'=>'URL',
			)
		);
		
		$this->addElement('text','sm_phone_o', 
			array(
				'label'=>'Phone'
			)
		);
		$this->sm_phone_o->addValidator('digits', true);
		
		
	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'highschool','action'=>'index'),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
		
	}
}
?>