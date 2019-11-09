<?php
class Application_Form_PlacementTestLocation extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','placementTestLocation');

		$this->addElement('text','al_location_name', array(
			'label'=>'Location Name',
			'required'=>true));
		
		$this->addElement('text','al_location_code', array(
			'label'=>'Code',
			'required'=>true));
		
		$this->addElement('text','al_address1', array(
			'label'=>'Address',
			'required'=>true));
		
		$this->addElement('text','al_address2', array(
			'label'=>''));
		
		$this->addElement('select','al_country', array(
			'label'=>'Country',
			'onchange'=>'changeCountry(this);'
		));
		$this->al_country->addMultiOption(null,"Select");
		$countryDb = new App_Model_General_DbTable_Country();
		foreach ($countryDb->getData() as $list){
			$this->al_country->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
		$this->addElement('select','al_state', array(
			'label'=>'State',
			'onchange'=>'changeState(this);'
		));
		$this->al_state->addMultiOption(null,"Select");
		$stateDb = new App_Model_General_DbTable_State();
		foreach ($stateDb->getData() as $list){
			$this->al_state->addMultiOption($list['idState'],$list['StateName']);
		}
		
		$this->addElement('select','al_city', array(
			'label'=>'City'));
		$this->al_city->addMultiOption(null,"Select");
		$cityDb = new App_Model_General_DbTable_City();
		foreach ($cityDb->getData() as $list){
			$this->al_city->addMultiOption($list['idCity'],$list['CityName']);
		}
		
		
		$this->addElement('text','al_phone', array(
			'label'=>'Phone'));
		
		$this->addElement('text','al_contact_person', array(
			'label'=>'Contact Person'));
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'placement-test-location','action'=>'index'),'default',true) . "'; return false;"
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