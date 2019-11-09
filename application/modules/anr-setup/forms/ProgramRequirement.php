<?php 
/**
 * Venue
 * 
 * @author Muhamad Alif
 * @version 
 */
class AnrSetup_Form_ProgramRequirement extends Zend_Form
{
	protected $programID;
	
	public function init()
	{
		$programDB = new App_Model_Record_DbTable_Program();
		$programList = $programDB->getData();
		
		$courseTypeDB = new App_Model_Record_DbTable_CourseType();
		
		
		$this->setMethod('post');
		$this->setAttrib('id','detailForm');
		$this->setAttrib('action',$this->getView()->url(array('module'=>'anr-setup', 'controller'=>'program-requirement','action'=>'addDetail', 'id'=>$this->programID)) );			
		
		/*** program ***/
		$this->addElement('text','program_name', array(
		'label'=>'Program Name',
		'required'=>'true'));
		
		//$this->element->program_name->setAttrib('disabled','true');
		
		/*$element = new Zend_Form_Element_Select('program_requirement_programID_disabled');
		$element	->setLabel('Program')
					->addMultiOption(0,"-- Select Program --");
		foreach ($programList as $list){
			$element->addMultiOption($list['id'],$list['main_name']);
		}		

		$element->setValue($this->programID); 
		$element->setAttrib('disabled','true');
		$this->addElement($element);*/
		
		$id = $this->createElement('hidden', 'program_id');
		$id->setValue($this->programID);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);
		

		/*** Course Type ***/
		$element = new Zend_Form_Element_Select('course_type_id');
		$element	->setLabel('Course Type')
					->setRequired('true')
					->addMultiOption(null,"--Select Course Type--");
		foreach ($courseTypeDB->getData() as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}				
		$this->addElement($element);
		
		/*** Min Credit ***/
		$this->addElement('text','credit_hour', array(
		'label'=>'Minimum Credit',
		'required'=>'true'));
		
		
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