<?php
class Exam_Form_Asscompitem extends Zend_Form
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
		$this->setAttrib('id','asscompitem_form');
		
					
		$component = new App_Model_Exam_DbTable_Asscomponent();
        $component_list = $component->selectAsscomponent();
		
		$this->addElement('select', 'component_id', array(
		    'label'=>'Component',
		    'required'=>'true',
		    'multioptions'=>$component_list));   
		
				
		$this->addElement('text','component_item_name', array(
			'label'=>'Component Item Name',
			'required'=>'true'));
			
		$this->addElement('text','component_item_weightage', array(
			'label'=>'Weightage',
			'required'=>'true'));
			
		$this->addElement('text','component_item_fullmark', array(
			'label'=>'Full Mark',
			'required'=>'true'));			
			
		$this->addElement('submit', 'submit', array(
		    'decorators' => $this->setDecorators,
		    'label' => 'Submit'
		));
					
		
				
		
	}
}
?>