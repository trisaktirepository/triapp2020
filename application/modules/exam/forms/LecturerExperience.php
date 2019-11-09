<?php
class Exam_Form_LecturerExperience extends Zend_Form
{
	protected $id;
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','exam_lecturer_experience_form');
		
		
		//id
		$hidden = $this->createElement('hidden', 'lecturer_id');
		$hidden->setValue($this->id);
		$this->addElement($hidden);
		
		//organization
        $this->addElement('text','organization', array(
			'label'=>'Organization',
			'required'=>'true',
			'filters'    => array('StringTrim'),
        ));
        
        //position
        $this->addElement('text','position', array(
			'label'=>'Position',
			'required'=>'true',
			'filters'    => array('StringTrim'),
        ));
        
        //subject taught
        $this->addElement('text','subject_taught', array(
			'label'=>'Subject Taught',
			'required'=>'true',
			'filters'    => array('StringTrim'),
        ));
		
		//Academic level
		$academic_levelDB = new Exam_Model_DbTable_AcademicLevel();
		$academic_level = $academic_levelDB->getData();
		
		$element = new Zend_Form_Element_Select('student_academic_level');
		$element	->setLabel('Student Level')
					->addMultiOption(0,"-- Select Student Academic Level -- ");
		foreach ($academic_level as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}
		$this->addElement($element);
		
		
		//year from
		$this->addElement('text','year_from', array(
			'label'=>'Year From',
			'required'=>'true',
			'filters'    => array('StringTrim'),
            'validators' => array(
                'digits',
            )
        ));
        
        //year to
		$this->addElement('text','year_to', array(
			'label'=>'Year To',
			'required'=>'true',
			'filters'    => array('StringTrim'),
            'validators' => array(
                'digits',
            )
        ));
	}
}
?>