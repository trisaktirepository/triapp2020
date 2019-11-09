<?php
class GeneralSetup_Form_Language extends Zend_Form
{
	
	public function init()
	{
	
		$this->setMethod('post');
		$this->setAttrib('id','setlanguage');

		$this->addElement('text','sl_var_name', array(
			'label'=>'Variable',
			'required'=>true));
		
		$this->addElement('text','sl_english', array(
			'label'=>'English',
			'required'=>true));
		
		$this->addElement('text','sl_bahasa', array(
			'label'=>'Bahasa',
			'required'=>true));
		

		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'language','action'=>'index'),'default',true) . "'; return false;"
        ));
	}
}
?>