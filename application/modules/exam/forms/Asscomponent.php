<?php
class Exam_Form_Asscomponent extends Zend_Form
{
	
	public $setDecorators = array(
        'ViewHelper',     
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
         array(array('label' => 'HtmlTag')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );
    
	public $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','asscomponent_form');
		
		$program = new App_Model_Record_DbTable_Program();
        $program_list = $program->getProgramList();
		
		$this->addElement('select', 'program_id', array(
		    'label'=>'Program',
		    'required'=>'true',
		    'multioptions'=>$program_list));  
				
		$courses = new App_Model_Record_DbTable_Course();
        $course_list = $courses->selectCourse();
		
		$this->addElement('select', 'course_id', array(
		    'label'=>'Course',
		    'required'=>'true',
		    'multioptions'=>$course_list));  		
		
		
		$this->addElement('text','component_name', array(
			'label'=>'Component Name',
			'required'=>'true'));
			
		$this->addElement('text','component_total_weightage', array(
			'label'=>'Total Weightage',
			'required'=>'true'));
			
		$this->addElement('text','component_passing_mark', array(
			'label'=>'Passing Mark',
			'required'=>'true'));			
			
		$this->addElement('submit', 'submit', array(
		    'decorators' => $this->setDecorators,
		    'label' => 'Submit'
		));
					
		
				
		
	}
}
?>