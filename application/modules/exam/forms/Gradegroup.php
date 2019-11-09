<?php
class Exam_Form_Gradegroup extends Zend_Form
{
	    
	public function init()
	{
		
		$this->setMethod('post');
		$this->setAttrib('id','grade_form');	
		
			
		$this->addElement('text','group_name', array(
			'label'=>'Group Name',
			'required'=>'true'));
			
		$this->addElement('submit', 'submit', array(
		    'label' => 'Submit'
		));
		
	}
	
	
	
	
	
	
}
?>