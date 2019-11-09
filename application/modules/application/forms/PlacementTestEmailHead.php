<?php
class Application_Form_PlacementTestEmailHead extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','form_email');

		$this->addElement('text','eth_template_name', array(
			'label'=>'Template Name',
			'required'=>true));
		
		$this->addElement('text','eth_email_from', array(
			'label'=>'Email From',
		));
		
		$this->addElement('text','eth_email_from_name', array(
			'label'=>'Name From',
		));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'placement-test-email','action'=>'index'),'default',true) . "'; return false;"
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