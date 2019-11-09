<?php
//require_once '../application/modules/setup/models/DbTable/Country.php';

class Admission_Form_Applicant extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','applicant_form');
	
		$this->addElement('text','applicantID', array(
			'label'=>'ID',
			'required'=>'true'));
			
		$this->addElement('text','firstname', array(
			'label'=>'Name',
			'required'=>'true'));
			
		$this->addElement('text','nric', array(
			'label'=>'NRIC',
			'required'=>'true'));
			
		$this->addElement('select','ARD_PROGRAM', array(
			'label'=>'Program',
			'required'=>'true'));
			
		
			
		$programDB = new Setup_Model_DbTable_Master();
		$program_data = $programDB->getMaster();
		
		$this->ARD_PROGRAM->addMultiOption(0,"-- Select Program --");
		foreach ($program_data as $list){
			$this->ARD_PROGRAM->addMultiOption($list['masterProgramID'],$list['masterProgram']);
		}
			
		$this->addElement('select','Gender', array(
			'label'=>'Gender',
			'required'=>'true'));
		$this->Gender->addMultiOption(0,"-- Select Gender --");
		$this->Gender->addMultiOption('M',"Male");
		$this->Gender->addMultiOption('F',"Female");
			
			
		$this->addElement('text','dob', array(
			'label'=>'DOB',
			'required'=>'true'));
			
		$this->addElement('text','citizen', array(
			'label'=>'Nationality',
			'required'=>'true'));

		$this->addElement('submit', 'submit', array(
		    'label' => 'Submit'
		));

	}
}
?>