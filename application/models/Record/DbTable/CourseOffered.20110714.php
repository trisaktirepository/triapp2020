<?php
class App_Model_Record_DbTable_CourseOffered extends Zend_Db_Table_Abstract
{
    protected $_name = 'r029_course_offer';
    protected $_primary = 'id';
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name)
					->where($this->_name.".".$this->_primary .' = '. $id);
					
				$row = $db->fetchRow($select);
		}else{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
								
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	
	public function getDataBySemester($semesterID=0, $departmentID=0){
		
		$semesterID = (int)$semesterID;
		
		if($semesterID!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			
			if($departmentID==0){
				$select = $db->select()
						->from(array('co'=>$this->_name))
						->where("co.semester_id = ". $semesterID)
						->joinLeft(array('c'=>'r010_course'), 'c.id = co.course_id',array('course_name'=>'name','course_code'=>'code','course_credit_hour'=>'credit_hour'))
						->joinLeft(array('d'=>'g008_department'), 'd.id = c.department_id',array('department_name'=>'name','department_code'=>'code'));
						
			}else{
				$select = $db->select()
						->from(array('co'=>$this->_name))
						->where("co.semester_id = ". $semesterID)
						->where('c.department_id = '.$departmentID)
						->join(array('c'=>'r010_course'), 'c.id = co.course_id',array('course_name'=>'name','course_code'=>'code','course_credit_hour'=>'credit_hour'))
						->joinLeft(array('d'=>'g008_department'), 'd.id = c.department_id',array('department_name'=>'name','department_code'=>'code'));	
			}
			
			
			$row = $db->fetchAll($select);
			
		}else{
			throw new Exception("There is No Data");
		}
		
		if(!$row){
			return null;
		}else{
			return $row;	
		}
		
		
	}
		
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name);
								
		return $select;
	}
	
    public function addCourseOffered($semesterID,$courseID)
    {
        $data = array(
            'semester_id' => $semesterID,
            'course_id' => $courseID,
        );
        $this->insert($data);
    }
    
    public function deleteCourseOffered($semesterID)
    {
        $this->delete('semester_id =' . (int)$semesterID);
    }
    
    public function getCourseOffered($program_id, $semester_id, $course_level=0){
    	//TODO: get courses from landscape
    	$db = Zend_Db_Table::getDefaultAdapter();
		$academic_landscape_select = $db->select()
				->from(array('al'=>'r012_academic_landscape'),array('id'=>'id'))
				->where('al.program_id = '.$program_id)
				->where('al.status = 1');

		$select = $db->select()
				->from(array('alc'=>'r013_academic_landscape_course'))
				->where('alc.academic_landscape_id in ('.$academic_landscape_select.')')
				->join(array('c'=>'r010_course'),'c.id = alc.course_id',array('course_name'=>'c.name','course_credit_hour'=>'c.credit_hour','course_code'=>'c.code'));
								
		if($course_level!=0){
			$select = $select->where('alc.level = '.$level);
		}				
//		echo $select;
		$row = $db->fetchAll($select);
		
		return $row;
    	
    }
    
    public function getCourseSemOffered($program_id, $semester_id, $course_level=0){
    	//TODO: get courses from landscape
    	$db = Zend_Db_Table::getDefaultAdapter();
		$academic_landscape_select = $db->select()
				->from(array('al'=>'r012_academic_landscape'),array('id'=>'id'))
				->where('al.program_id = '.$program_id)
				->where('al.status = 1');

		$select = $db->select()
				->from(array('alc'=>'r013_academic_landscape_course'))
				->where('alc.academic_landscape_id in ('.$academic_landscape_select.')')
				->join(array('c'=>'r010_course'),'c.id = alc.course_id',array('course_name'=>'c.name','course_credit_hour'=>'c.credit_hour','course_code'=>'c.code'))
				->join(array('of'=>'r029_course_offer'),'of.course_id = alc.course_id and of.semester_id = '.$semester_id,array('semester_id'=>'of.semester_id'));
								
		if($course_level!=0){
			$select = $select->where('alc.level = '.$level);
		}				
//		echo $select;
		$row = $db->fetchAll($select);
		
		return $row;
    	
    }
    
}