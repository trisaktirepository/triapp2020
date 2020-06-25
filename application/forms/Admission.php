<?php
class App_Form_Admission extends Zend_Form {
	
	public function init(){
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','admission_form');
		
		
		
		$this->addElement('radio','at_appl_type', array(
			'label'=>'Admission Type'	   
				
		));		
		
		
		$this->at_appl_type->setMultiOptions(array('1'=>' '.$this->getView()->translate('Placement Test'),
				'2'=>' '.$this->getView()->translate('High School Certificate'),
				'3'=>' '.$this->getView()->translate('Credit Transfer'),
				'4'=>' '.$this->getView()->translate('Invitation'),
				'5'=>' '.$this->getView()->translate('Portofolio'),
				'8'=>' '.$this->getView()->translate('Portofolio Magister/Doctor'),
				'6'=>' '.$this->getView()->translate('Scholarship'),
				'7'=>' '.$this->getView()->translate('Nilai UTBK')))->setRequired(true);
		$this->at_appl_type->setValue(1);
			
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'contactinfo'),'default',true) . "'; return false;"
        ));
	}
	
}
?>