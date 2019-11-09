<?php
class Exam_Form_Markadjsetup extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','markadjsetup_form');
		
		  
        $semester = new App_Model_Record_DbTable_Semester();
        $semester_list = $semester->listSemester();
		
		$this->addElement('select', 'semester_id', array(
			'label'=>'Semester',
			'required'=>'true',
		    'multioptions'=>$semester_list));

		$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getProgramList();
      
		
		$this->addElement('select', 'program_id', array(
		    'label'=>'Program',
		    'required'=>'true',
		    'multioptions'=>$program_list));  		
		
				
		$this->addElement('text','min_mark', array(
			'label'=>'Mark Range From',
			'required'=>'true'));
			
		$this->addElement('text','max_mark', array(
			'label'=>'Mark Range To',
			'required'=>'true'));

		$this->addElement('text','value', array(
			'label'=>'Value',
			'required'=>'true'));
			
		$this->addElement('submit', 'submit', array(
		    'label' => 'Submit'
		));
		
	}
}
?>