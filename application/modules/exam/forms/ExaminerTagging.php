<?php
class Exam_Form_ExaminerTagging extends Zend_Form
{
	protected $semesterID, $uid;
	
	
	public function setSemesterID($semesterID){
		$this->semesterID = $semesterID;
	}
	
	public function setUid($uid){
		$this->uid = $uid;
	}
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','exam_examiner_tagging_form');
		
		//semester
		$hidden1 = $this->createElement('hidden', 'semester_id');
		$hidden1->setValue($this->semesterID);
		$this->addElement($hidden1);
		
		
		//examiner
		$hidden2 = $this->createElement('hidden', 'examiner_id');
		$hidden2->setValue($this->uid);
		$this->addElement($hidden2);
		
		
		//Course Offered
		$course_offeredDB = new App_Model_Record_DbTable_CourseOffered();
		$courses = $course_offeredDB->getDataBySemester($this->semesterID);
		
		$element = new Zend_Form_Element_Select('course_id');
		$element	->setLabel('Course')
					->addMultiOption(0,"-- Select Course -- ");
		foreach ($courses as $list){
			$element->addMultiOption($list['course_id'],$list['course_code']. " - " .$list['course_name']);
		}
 		
		$this->addElement($element);
		
		
		//Distribution Role
		$element = new Zend_Form_Element_Select('component_id');
		$element->setLabel('Component allowed')
				->addMultiOption(-1,"-- Select Component --")
				->setRequired(true)
				->setRegisterInArrayValidator(false);
		
		$this->addElement($element);
		
	}
}
?>