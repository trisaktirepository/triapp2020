<?php
class Setup_Form_Asscomponent extends Zend_Form
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
		
		$masterprogram = new Setup_Model_DbTable_MasterProgram();
        $masterprogram_list = $masterprogram->getMasterProgramList();
		
		$this->addElement('select', 'program_id', array(
		    'label'=>'Program',
		    'required'=>'true',
		    'multioptions'=>$masterprogram_list));   
		
		$course = new Setup_Model_DbTable_Course();
        $masterprogram_list = $course->getMasterProgramList();
		
		$this->addElement('select', 'program_id', array(
		    'label'=>'Program',
		    'required'=>'true',
		    'multioptions'=>$masterprogram_list));   
		
		
		
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