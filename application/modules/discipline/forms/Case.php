<?php
class Discipline_Form_Case extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','case_form');
		
		$this->addElement('text','case_name', array(
			'label'=>'Name',
			'required'=>'true'));
		
		$this->addElement('text','case_code', array(
		'label'=>'Code',
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
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'discipline', 'controller'=>'case','action'=>'add')) . "'; return false;"
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