<?php
class Exam_Form_Grade extends Zend_Form
{
	protected $groupID;
	
	
    public function setGroupID($groupID){
		$this->groupID = $groupID;
	}
	
    

	public function init()
	{
		
		$this->setMethod('post');
		$this->setAttrib('id','grade_form');	
	
		
		$this->addElement('hidden','grade_group_id',array('value' =>$this->groupID));
		
					
		$this->addElement('text','symbol', array(
			'label'=>'Grade Symbol',
			'required'=>'true'));
			
		$this->addElement('text','point', array(
			'label'=>'Point Grade',
			'required'=>'true'));
			
		$this->addElement('text','status', array(
			'label'=>'Grade Status',
			'required'=>'true'));
			
		$this->addElement('text','min_mark', array(
			'label'=>'Minimum Mark',
			'required'=>'true'));
			
		$this->addElement('text','max_mark', array(
			'label'=>'Maximum Mark',
			'required'=>'true'));
			
		$this->addElement('submit', 'submit', array(
		    'label' => 'Submit'
		));
		
	}
	
	
	
	
	
	
}
?>