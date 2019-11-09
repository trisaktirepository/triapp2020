<?php
class AnrSetup_Form_Program extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','program_form');
		
		/*-------------------------Program Name -------------------------*/
		$this->addElement('text','program_name', array(
			'label'=>'Program Name',
			'required'=>'true'));
		
		/*----------------------Master Program ID------------------------*/
		/*$this->addElement('select','program_main_id', array(
			'label'=>'Program',
			'required'=>'true'));
		
		$masterDB = new App_Model_Record_DbTable_MainProgram();
		$master_data = $masterDB->getData();
		
		$this->program_main_id->addMultiOption(0,"-- Select Program --");
		foreach ($master_data as $list){
			$this->program_main_id->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('text','code', array(
			'label'=>'Program Code',
			'required'=>'true'));*/
		
		/*----------------------Market------------------------------------*/
		/*$this->addElement('select','market_id', array(
			'label'=>'Learning Mode',
			'required'=>'true'));
		
		$marketDB = new App_Model_Record_DbTable_Market();
		$market_data = $marketDB->getData();
		
		$this->market_id->addMultiOption(0,"-- Select Learning Mode --");
		foreach ($market_data as $list){
			$this->market_id->addMultiOption($list['id'],$list['name']);
		}*/
			
		/*----------------------Faculty------------------------------------*/
		$this->addElement('select','faculty_id', array(
			'label'=>'Department',
			'required'=>'true'));
		
		$facultyDB = new App_Model_General_DbTable_Faculty();
		$faculty_data = $facultyDB->getData();
		
		$this-> faculty_id->addMultiOption(0,"-- Select Faculty --");
		foreach ($faculty_data as $list){
			$this->faculty_id->addMultiOption($list['id'],$list['name']);
		}
		
		/*----------------------Department------------------------------------*/
		/*$this->addElement('select','department_id', array(
			'label'=>'Department'));
		
		$departmentDB = new App_Model_General_DbTable_Department();
		$department_data = $departmentDB->getData();
		
		$this->department_id->addMultiOption(0,"-- Select Department --");
		foreach ($department_data as $list){
			$this->department_id->addMultiOption($list['id'],$list['name']);
		}*/
		
		
		/*----------------------Award------------------------------------*/
		$this->addElement('select','award_id', array(
			'label'=>'Award',
			'required'=>'true'));
		
		$awardDB = new App_Model_Record_DbTable_Award();
		$award_data = $awardDB->getData();
		
		$this->award_id->addMultiOption(0,"-- Select Award --");
		foreach ($award_data as $list){
			$this->award_id->addMultiOption($list['id'],$list['name']);
		}
		
		/*$this->addElement('text','min_student', array(
			'label'=>'Minimum before open',
			'required'=>'true'));*/
		
		
		/*$this->addElement('text','duration', array(
			'label'=>'Min Years Duration',
			'required'=>'true'));*/
		
		$this->addElement('checkbox','status', array(
			'label'=>'Active?',
			'required'=>'true'));
		
		
		
		
		
		/*----------------------First Intake------------------------------------*/
		/*$this->addElement('select','first_intake_id', array(
			'label'=>'First Intake',
			'required'=>'true'));
		
		$intakeDB = new App_Model_Record_DbTable_Intake();
		$intake_data = $intakeDB->getIntake();
		
		$this->first_intake_id->addMultiOption(0,"-- Select Intake --");
		foreach ($intake_data as $data){
			$this->first_intake_id->addMultiOption($data['id'],$data['name']);
		}*/
		
		//synopsis
		$this->addElement('textarea','synopsis', array(
			'label'=>'Synopsis'));
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'anr-setup', 'controller'=>'program','action'=>'index'),'default',true) . "'; return false;"
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