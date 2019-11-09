<?php
class GeneralSetup_Form_Disctrict extends Zend_Form
{
	protected $stateID;
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','disctrict_form');
		
		
		/*State ID*/		
		$stateDB = new App_Model_General_DbTable_State();
		$state_data = $stateDB->getData();
		
		$element = new Zend_Form_Element_Select('state_id_disable');
		$element	->setLabel('Governorate')
					->addMultiOption(0,"-- Select Governorate --");
		foreach ($state_data as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}		

		$element->setValue($this->stateID); 
		$element->setAttrib('disabled','true');
		$this->addElement($element);
		
		$id = $this->createElement('hidden', 'state_id');
		$id->setValue($this->stateID);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);
		
		$this->addElement('text','name', array(
			'label'=>'Disctrict',
			'required'=>'true'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'state','action'=>'view','id'=>$this->stateID),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));

	}
	
	public function setStateID($stateID){
		$this->stateID = $stateID;
	}
}
?>