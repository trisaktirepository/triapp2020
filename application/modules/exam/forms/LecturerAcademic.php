<?php
class Exam_Form_LecturerAcademic extends Zend_Form
{
	protected $id;
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','exam_lecturer_academic_form');
		
		
		//id
		$hidden = $this->createElement('hidden', 'lecturer_id');
		$hidden->setValue($this->id);
		$this->addElement($hidden);
		
		//Academic level
		$academic_levelDB = new Exam_Model_DbTable_AcademicLevel();
		$academic_level = $academic_levelDB->getData();
		
		$element = new Zend_Form_Element_Select('academic_level');
		$element	->setLabel('Academic Level')
					->addMultiOption(0,"-- Select Academic Level -- ");
		foreach ($academic_level as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}
		$this->addElement($element);
		
		
		//year
		$this->addElement('text','year', array(
			'label'=>'Year',
			'required'=>'true',
			'filters'    => array('StringTrim'),
            'validators' => array(
                'digits',
            )
        ));
        
        //major
        $this->addElement('text','major', array(
			'label'=>'Major',
			'required'=>'true',
			'filters'    => array('StringTrim'),
        ));
        
        //institution
        $this->addElement('text','institution', array(
			'label'=>'Institution',
			'required'=>'true',
			'filters'    => array('StringTrim'),
        ));
	}
}
?>