<?php 

class Application_Form_Requirement extends Zend_Form
{
	protected $programID;
	
	public function init()
	{
		$programDB = new App_Model_Record_DbTable_Program();
		$programList = $programDB->getData();
		
		$courseTypeDB = new App_Model_Application_DbTable_EducationLevel();
		
		$conditionDB = new App_Model_Application_DbTable_Condition();
		
		
		$this->setMethod('post');
		$this->setAttrib('id','detailForm');
		$this->setAttrib('action',$this->getView()->url(array('module'=>'application', 'controller'=>'entry-requirement','action'=>'adddetail', 'id'=>$this->programID)) );			
		
				

		
		
		$id = $this->createElement('hidden', 'program_id');
		$id->setValue($this->programID);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);
		
		/*** program ***/
		$element = new Zend_Form_Element_Select('program_requirement_programID_disabled');
		$element	->setLabel('Programme')
					->addMultiOption(0,"-- Select Programme --");
		foreach ($programList as $list){
			$element->addMultiOption($list['id'],$list['program_name']);
		}
		
		$element->setValue($this->programID); 
		$element->setAttrib('disabled','true');
		$this->addElement($element);
		
		/*** Description Requirement ***/
		$this->addElement('textarea','desc', array(
		'label'=>'Description',
		'required'=>'true'));

		/*** Education Level ***/
		$this->addElement('select','education_level', array(
			'label'=>'Education Level',
			'required'=>'true'));
		
		$this->education_level->addMultiOption(null,"-- Select Education Level --");
		foreach ($courseTypeDB->getProgram() as $list){
			$this->education_level->addMultiOption($list['id'],$list['name']);
		}
		
		
		/** Qualification item **/
		$qualificationItemDB = new App_Model_Application_DbTable_QualificationItem();
		$element = new Zend_Form_Element_Select('item');
		$element	->setLabel('Qualification Item')
					->addMultiOption(null,"--Optional--");
		foreach ($qualificationItemDB->getData() as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}				
		$this->addElement($element);
		
		
		/*** Condition Type ***/
		$element = new Zend_Form_Element_Select('condition');
		$element	->setLabel('Condition')
					->addMultiOption(null,"--Optional--");
		foreach ($conditionDB->getData() as $list){
			$element->addMultiOption($list['symbol'],$list['condition_name']);
		}				
		$this->addElement($element);
		
		/*** Min Credit ***/
		$this->addElement('text','value', array(
		'label'=>'Result/Grade'));
		
		$this->addElement('checkbox','compulsory', array(
			'label'=>'Compulsory?'));

		
		
		//button
		/*$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'setup', 'controller'=>'program-requirement','action'=>'view', 'program-id'=>$this->programID)) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));*/
	}
	
	public function setProgramID($programID){
		$this->programID = $programID; 
	}
}
?>