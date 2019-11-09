<?php 
class App_Model_Record_DbTable_ProgramCourse extends Zend_Db_Table_Abstract
{
    protected $_name = 'r034_program_course';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db ->select()
						->from(array('program_course'=>$this->_name))
						->where('program_course.'.$this->_primary.' = ' .$id)
						->joinLeft(array('course'=>'r010_course'),'course.id = program_course.course_id',array('course_name'=>'name','course_code'=>'code','lms_code'=>'lmsCode'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db ->select()
						->from(array('program_course'=>$this->_name))
						->joinLeft(array('course'=>'r010_course'),'course.id = program_course.course_id',array('course_name'=>'name','course_code'=>'code','lms_code'=>'lmsCode'))
						->order(array('course.name'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
//		if(!$row){
//			throw new Exception("There is No Program");
//		}
		
		return $row;
	}
	
	public function getPaginateData($programid=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
						->from(array('program_course'=>$this->_name))
						->joinLeft(array('course'=>'r010_course'),'course.id = program_course.course_id',array('course_name'=>'name','course_code'=>'code','lms_code'=>'lmsCode'));
						
		if($programid!=0){
			$selectData->where('program_course.'.$this->_primary.' = ' .$programid);
		}

		return $selectData;
	}
	
	public function addData($postData){
		$data = array(
		        'program_id' => $postData['program_id'],
				'course_id' => $postData['course_id'],
				'created_by' => $postData['created_by'],
				'date_created' => date("d/m/y H:i:s")
				);
			
		$this->insert($data);
	}
	
	
	public function deleteData($id){
		$this->delete('id =' . (int)$id);
	}
	
	public function getProgramCourse($program_id=0)	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
						->from(array('program_course'=>$this->_name))
						->where('program_course.program_id = ?',$program_id)
						->joinLeft(array('course'=>'r010_course'),'course.id = program_course.course_id',array('course_name'=>'name','course_code'=>'code','lms_code'=>'lmsCode'));
			                     
	  
        $result = $db->fetchAll($select);  
        
        if(!$result){
        	return null;	
        }else{
        	return $result;
        }
	    
	}
}
?>