<?php
class Application_Form_Salutation extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','salutation_form');

		/*----------------------Education Name------------------------------------*/
		$this->addElement('text','name', array(
			'label'=>'Salutation Name',
			'required'=>'true'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'salutation','action'=>'index'),'default',true) . "'; return false;"
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