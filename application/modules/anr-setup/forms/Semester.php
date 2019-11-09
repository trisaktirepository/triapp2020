<?php
class AnrSetup_Form_Semester extends Zend_Form
{
    public function init()
	{
		$masterprogramDB = new App_Model_Record_DbTable_MainProgram();
		$programDB = new App_Model_Record_DbTable_Program();		
		$semesterDB = new App_Model_Record_DbTable_Semester();		
		
		$this->setMethod('post');
		$this->setAttrib('id','semester_form')
		->setAttrib('name','semester_form');

		
		$this->addElement('text','name', array(
			'label'=>'Semester Name',
			'required'=>'true'
			));
		
		$this->addElement('text','code', array(
			'label'=>'Semester Code'
			));
			
		$this->addElement('text','start_date', array(
			'label'=>'Start Date',
			'required'=>'true'));
		
		$this->addElement('text','end_date', array(
			'label'=>'End Date',
			'required'=>'true'));
		
	}}