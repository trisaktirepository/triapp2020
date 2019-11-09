<?php

 error_reporting(E_ALL);
 ini_set("display_errors", 1);
 
 
class AnrSetup_Form_SemesterName extends Zend_Form
{
    public function init()
	{
		
		$this->setMethod('post');
		$this->setAttrib('id','semestername_form');

		
		$this->addElement('text','name', array(
			'label'=>'Semester Name',
			'required'=>'true'
			));
		
		$this->addElement('checkbox','status', array(
			'label'=>'Active?',
			'required'=>'true'));
			
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'anr-setup', 'controller'=>'semester','action'=>'addname'),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
		
	}}