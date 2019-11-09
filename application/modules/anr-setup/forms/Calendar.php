<?php 
class AnrSetup_Form_Calendar extends Zend_Form
{
	
	public function init()
	{
		        
		$this->setMethod('post');
		$this->setAttrib('id','company_form');
		
		//semester
		$element = new Zend_Form_Element_Select('semester_id');
		$element->setLabel('Semester')
							->addMultiOption(0,"-- Select Semester --")
							->setRequired('true');
		foreach ($this->getSemester() as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}
		$this->addElement($element);
		
		//activity
		$element = new Zend_Form_Element_Select('activity_id');
		$element->setLabel('Activity')
							->addMultiOption(0,"-- Select Activity --")
							->setRequired('true');
		foreach ($this->getActivity() as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}
		$this->addElement($element);
		
		//start date
		$this->addElement('text','start_date', array(
			'label'=>'Start Date',
			'required'=>'true'
		));
		
		//end date
		$this->addElement('text','end_date', array(
			'label'=>'End Date',
			'required'=>'true'
		));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'anr-setup', 'controller'=>'calendar','action'=>'index')) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
	}
	
	
	private function getSemester(){
		$semesterDB = new App_Model_Record_DbTable_Semester();
		
		$data = $semesterDB->getData();
		
		return $data;
	}
	
	
	private function getActivity(){
		$activityDB = new App_Model_Record_DbTable_Activity();
		
		$data = $activityDB->getData();
		
		return $data;
	}
}
?>