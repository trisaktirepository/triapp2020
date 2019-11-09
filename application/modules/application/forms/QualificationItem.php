<?php
class Application_Form_QualificationItem extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','educationlevel_form');

		/*----------------------Education Name------------------------------------*/
		$this->addElement('text','name', array(
			'label'=>'Qualification Item',
			'required'=>'true'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'qualification-item','action'=>'index'),'default',true) . "'; return false;"
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