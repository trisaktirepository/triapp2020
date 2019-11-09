<?php

class GeneralSetup_Form_SchoolSubject extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','form_ss');
		
		$this->addElement('text','ss_subject', 
			array(
				'label'=>'Subject Name',
				'required'=>'true'
			)
		);
		
		$this->addElement('text','ss_subject_bahasa', 
			array(
				'label'=>'Subject Name (Indonesia)'
			)
		);
		
		$this->addElement('checkbox','ss_core_subject', 
			array(
				'label'=>'Basic Core Subject'
			)
		);
		
	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'highschool-subject','action'=>'index'),'default',true) . "'; return false;"
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