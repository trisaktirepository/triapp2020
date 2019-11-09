<?php
class AnrSetup_Form_CourseOffered extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','country_form');

		/*Semester*/
		/*$semesterDB = new Setup_Model_DbTable_Semester();
		$semester_data = $semesterDB->getData();
		
		$element = new Zend_Form_Element_Select('course_offered_semesterID');
		$element->setLabel('Semester')
				->setRequired('true');
		$element->addMultiOption(null,'-- Select Semester --');
		foreach ($semester_data as $semester){
			$element->addMultiOption($semester['semesterID'],$semester['semester']);
		}
		$this->addElement($element);*/
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'setup', 'controller'=>'course-offered','action'=>'index'),'default',true) . "'; return false;"
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