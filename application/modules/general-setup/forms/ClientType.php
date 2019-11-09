<?php
class GeneralSetup_Form_ClientType extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','client_type_form');
		
		$this->addElement('text','name', array(
			'label'=>'Name',
			'required'=>'true'));
		
		$this->addElement('textarea','description', array(
			'label'=>'Description'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'client','action'=>'type')) . "'; return false;"
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