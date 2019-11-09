<?
class Agent_Form_ApplicantAdmission extends Zend_Form {
	protected $admission_type;
	
	public function setAdmission_type($admission_type){
		$this->admission_type = $admission_type;
	}
	
	public function init(){
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','admission_form');
		
		
		$this->addElement('radio','at_appl_type', array(
			'label'=>'Admission Type'	   
				
		));		
		
		
		$this->at_appl_type->setMultiOptions(array('1'=>' '.$this->getView()->translate('Placement Test'),'2'=>' '.$this->getView()->translate('High School Certificate')))->setRequired(true);
		$this->at_appl_type->setValue($this->admission_type);
			
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'agent', 'controller'=>'index','action'=>'contactinfo'),'default',true) . "'; return false;"
        ));
	}
	
}
?>