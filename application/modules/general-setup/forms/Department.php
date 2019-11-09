<?php

class GeneralSetup_Form_Department extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','office_form');
		
		//Faculty
		$this->addElement('select','faculty_id', array(
			'label'=>'Faculty'
		));
		
		$facultyDB = new App_Model_General_DbTable_Faculty();
		$faculty_data = $facultyDB->getData();
		
		$this->faculty_id->addMultiOption(null,"-- Select Faculty --");
		foreach ($faculty_data as $list){
			$this->faculty_id->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('text','name', array(
			'label'=>'Department Name',
			'required'=>'true'));
		
		$this->addElement('text','code', array(
			'label'=>'Department Code'));
		

	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'department','action'=>'index'),'default',true) . "'; return false;"
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