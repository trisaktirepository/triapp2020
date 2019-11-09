<?php 
class App_Model_Tos_DbTable_StudentExamQuestion extends Zend_Db_Table_Abstract
{
    protected $_name = 'q018_student_exam_set';
    protected $_subname = 'q019_student_exam_question';
	protected $_primary = "id";
	protected $_subprimary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('tm'=>$this->_name))
	                 ->where('tm.'.$this->_primary.' = ' .$id);
	               
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		
		return $row;
	}
	
	public function getSet($regId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                 ->from(array('ex'=>$this->_name))
	                 ->where("student_regID LIKE '".$regId."'")
	                 ->joinleft(array('p'=>'q001_pool'),'p.id=ex.exam_set_bank_id',array('bname'=>'p.name'));			                     
                         
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        return $row;
	}
	
	
	public function getQuestion($student_exam_set_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                 ->from(array('seq'=>$this->_subname))
	                 ->joinleft(array('q'=>'q003_question'),'q.id=seq.question_id',array('qid'=>'q.id','english'=>'q.english','q.malay'=>'malay'))
	                 ->where("seq.student_exam_set_id =".$student_exam_set_id);
	                			                     
                         
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        return $row;
	}
	
	public function addData($data){		
		return $id = $this->insert($data);
	}
	
	public function addDetails($data){	
		$db = Zend_Db_Table::getDefaultAdapter();	
		return $id = $db->insert($this->_subname,$data);
	}
	
	public function updateData($data,$id){		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function updateDetails($data,$id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->update($this->_subname,$data,$this->_subprimary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function deleteDetails($id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->delete($this->_subname,'student_exam_set_id =' . (int)$id);
	}
}
?>