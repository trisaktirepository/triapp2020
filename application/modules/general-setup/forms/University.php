<?php
class GeneralSetup_Form_University extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','university_form');

		$this->addElement('text','name', array(
			'label'=>'Name',
			'id'=>'title',
			'required'=>'true'));
			
		$this->addElement('text','address1', array(
			'label'=>'Address'));
		
		$this->addElement('text','address2', array(
			'label'=>''));
		
		$this->addElement('text','city', array(
			'label'=>'City'));
		
		//STATE
		$this->addElement('select','state_id', array(
			'label'=>'State',
			'inArrayValidator'=>'false'
		
		));
		
		$countryDB = new App_Model_General_DbTable_State();
		$country_data = $countryDB->getData();
		
		$this->state_id->addMultiOption(0,"-- Select State --");
		foreach ($country_data as $list){
			$this->state_id->addMultiOption($list['id'],$list['name']);
		}
		
		//COUNTRY
		$this->addElement('select','country_id', array(
			'label'=>'Country',
			'inArrayValidator'=>'false'
		
		));
		
		$countryDB = new App_Model_General_DbTable_Country();
		$country_data = $countryDB->getData();
		
		$this->country_id->addMultiOption(0,"-- Select Country --");
		foreach ($country_data as $list){
			$this->country_id->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('text','postcode', array(
			'label'=>'Postcode'));
		
		$this->addElement('text','phone', array(
			'label'=>'Phone'));
		
		$this->addElement('text','email', array(
			'label'=>'Email'));
		
		$this->addElement('text','url', array(
			'label'=>'URL'));
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'university','action'=>'index'),'default',true) . "'; return false;"
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