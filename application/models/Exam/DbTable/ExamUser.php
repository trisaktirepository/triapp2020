<?php

class App_Model_Exam_DbTable_ExamUser extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e010_exam_user';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Info");
		}			
		return $row->toArray();
	}
	
	public function getCourseExamUser($program_id,$user_id){
		$db = Zend_Db_Table::getDefaultAdapter();
    	  
    	$select= $db ->select()
    	             ->from(array('exam_user'=>$this->_name))
    	             ->join(array('course'=>'r010_course'),'course.id=exam_user.course_id',array('name'=>'name','code'=>'code'))
    	             ->where('exam_user.user_id   = ?',$user_id)
    	             ->where('exam_user.program_id = ?',$program_id);
   			$stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	     
	        return $row;
	}
	
	public function addData($data){
		
		$this->insert($data);
	}
	
	public function deleteData($id){		
			$this->delete($this->_primary . ' = ' . (int)$id);
	}
}
?>