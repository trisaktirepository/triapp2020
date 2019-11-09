<?php
class GeneralSetup_Form_State extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','state_form');
		
		
		/*Country ID*/
		$this->addElement('select','country_id', array(
			'label'=>'Country',
			'required'=>'true'));
		
		$countryDB = new App_Model_General_DbTable_Country();
		$country_data = $countryDB->getData();
		
		$this->country_id->addMultiOption(0,"-- Select Country --");
		foreach ($country_data as $list){
			$this->country_id->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('text','name', array(
			'label'=>'Governorate',
			'id'=>'title',
			'required'=>'true'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'state','action'=>'index'),'default',true) . "'; return false;"
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