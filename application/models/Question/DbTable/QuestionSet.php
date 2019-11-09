<?php 
class App_Model_Question_DbTable_QuestionSet extends Zend_Db_Table_Abstract
{
    protected $_name = 'q010_setcandidate';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('u'=>$this->_name))
	                 ->where('u.'.$this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        $row =  $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	
	public function checkSet($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 echo  $select = $db->select()
		             ->from(array('s' => $this->_name))		
					 ->where('s.idApplication='.$id);
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	public function getAnswerbyQuestion($idMainQues,$idQuestion){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('a' => $this->_name))		
					 ->where('a.question_main_id='.$idMainQues)
					 ->where('a.question_id='.$idQuestion);
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
public function getCorrectAnswer($idMainQues,$idQuestion){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('a' => $this->_name))		
					 ->where('a.question_main_id='.$idMainQues)
					 ->where('a.question_id='.$idQuestion)
					 ->where('a.correct_answer = 1');
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
		return $selectData;
	}
	
	public function addData($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$this->insert($data);
		$id = $db->lastInsertId();
        
        return $id;
	}
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>