<?php 
/**
 * Venue
 * 
 * @author Muhamad Alif
 * @version 
 */
class AnrSetup_Form_AcademicLandscapeCourse extends Zend_Form
{
	protected $programID,$academicID;
	
	public function init()
	{
		
		$programDB = new App_Model_Record_DbTable_Program();
		$programList = $programDB->getData();
		
		$courseDB = new App_Model_Record_DbTable_Course();
		$courseTypeDB = new App_Model_Record_DbTable_CourseType();
		
		
		$this->setMethod('post');
		$this->setAttrib('id','progreq_setup_form');			
		
		/*** academic landscape id ***/
		$element = new Zend_Form_Element_Hidden('academic_landscape_id');
		$element->setValue($this->academicID);
		$this->addElement($element);
		
		/*** program ***/
		$element = new Zend_Form_Element_Select('program_requirement_programID_disabled');
		$element	->setLabel('Program')
					->addMultiOption(0,"-- Select Program --");
		foreach ($programList as $list){
			$element->addMultiOption($list['id'],$list['main_name']);
		}		

		$element->setValue($this->programID); 
		$element->setAttrib('disabled','true');
		$this->addElement($element);
		
		$id = $this->createElement('hidden', 'program_ID');
		$id->setValue($this->programID);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);
		
		
		/*** Course ***/
		$element = new Zend_Form_Element_Select('course_id');
		$element	->setLabel('Course')
					->setRequired('true')
					->addMultiOption(null,"-- Select Course --");
		foreach ($courseDB->getData()as $list){
			$element->addMultiOption($list['id'],$list['code']." - ".$list['name']);
		}				
		$this->addElement($element);

		/*** Course Type ***/
		$element = new Zend_Form_Element_Select('course_type_id');
		$element	->setLabel('Course Type')
					->setRequired('true')
					->addMultiOption(null,"-- Select Course Type --");
		foreach ($courseTypeDB->getData() as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}				
		$this->addElement($element);
		
		/*** Level ***/
		$element = new Zend_Form_Element_Select('level');
		$element	->setLabel('Level')
					->setRequired('true')
					->addMultiOption(null,"-- Select Level --");
		for($i=1;$i<21;$i++){
			$element->addMultiOption($i,$i);
		}				
		$this->addElement($element);
		
		
		/*** Transferable ***/
		$istransfer = new Zend_Form_Element_Checkbox('is_transferable');
        $istransfer->setLabel('Allow Credit Transfer?');
 
        $this->addElement($istransfer);
        
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'anr-setup', 'controller'=>'academic-landscape','action'=>'view-detail', 'program-id'=>$this->programID, 'id'=>$this->academicID),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
	}
	
	public function setProgramID($programID){
		$this->programID = $programID;
	}
	
	public function setAcademicID($academicID){
		$this->academicID = $academicID;
	}
}
?>