<?php 
class App_Model_Question_DbTable_Question extends Zend_Db_Table_Abstract
{
    protected $_main = 'q003_question';
    protected $_name = 'q003_question';
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
	
	
	public function getQuestion($condition=null){
		
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		   $select = $db->select()
		             ->from(array('q'=>$this->_main))		
		             ->joinLeft(array('c' =>'q002_chapter'),'c.id = q.topic_id',array('topic_name'=>'c.name'))	             		            
		             ->joinLeft(array('qt'=>'q005_question_type'),'qt.id = q.question_type',array('question_name'=>'qt.name'))	;
		                      		            
					
	 
		if($condition!=null){
			if(isset($condition["pool_id"])){
				if($condition["pool_id"]!=""){
				$select->where('q.pool_id='.$condition["pool_id"]);
				}
			}	
			
			if(isset($condition["topic_id"]) ){
				if($condition["topic_id"]!="")$select->where('q.topic_id='.$condition["topic_id"]);				
			}
			
			if(isset($condition["question"]) ){	
				if($condition["question"]!="")	{		
				$select->where("q.english LIKE '%".$condition['question']."%'");
				$select->orwhere("q.malay LIKE '%".$condition['question']."%'");
				}
			}
			
			if(isset($condition["difficulty_level"]) ){
				if($condition["difficulty_level"]!="")$select->where('q.difficulty_level='.$condition["difficulty_level"]);
			}
			
			if(isset($condition["question_id"]) ){
				if($condition["question_id"]!="")$select->where('q.id='.$condition["question_id"]);
			}
						
			if(isset($condition["qid"]) ){
				if($condition["qid"]!="")$select->where('q.id='.$condition["qid"]);
			}
		}

		if(isset($condition["qid"])) {
			$row = $db->fetchRow($select);  
		}else{
        	$row = $db->fetchAll($select);  
		}      
	      
		
		return $row;
	}
	
	public function getTotalQuestion($condition=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		   $select = $db->select()
		             ->from(array('q'=>$this->_main))		
		             ->joinLeft(array('c' =>'q002_chapter'),'c.id = q.topic_id',array('topic_name'=>'c.name'))	             		            
		             ->joinLeft(array('qt'=>'q005_question_type'),'qt.id = q.question_type',array('question_name'=>'qt.name'))	;             		            
					
	 
		if($condition!=null){
			if(isset($condition["topic_id"])){
				$select->where('q.topic_id='.$condition["topic_id"]);
			}
			
			if(isset($condition["difficulty_level"])){
				$select->where('q.difficulty_level='.$condition["difficulty_level"]);
			}
			
			if(isset($condition["pool_id"])){
				$select->where('q.pool_id='.$condition["pool_id"]);
			}
			
			if(isset($condition["qid"])){
				$select->where('q.id='.$condition["qid"]);
			}
		}

		if(isset($condition["qid"])) {
			$row = $db->fetchRow($select);  
		}else{
        	$row = $db->fetchAll($select);  
		}      
	      
		return count($row);
	}
	
	
	public function getQuestionPool($pool_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		             ->from(array('q'=>$this->_main))
		             ->joinLeft(array('p'=>'q001_pool'),'p.id = q.pool_id',array('pool_name'=>'p.name','pool_id'=>'p.id'))		            
					 ->where('q.pool_id='.$pool_id);
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	
	public function generateSetQuestionStudent($idApplication){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		             ->from(array('a' => 'r015_student'))		
		             ->joinLeft(array('b'=>'r016_registrationdetails'),'b.idApplication = a.ID',array('*'))
					 ->where('a.ID = '.$idApplication);
	 
        $row = $db->fetchAll($select);	      
		return $row;
	}
	
	
	public function getQuestionTos($pool,$difficulty,$chapter){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		  $select = $db->select()
		             ->from(array('a' => 'q003_question'))		
					 ->where('a.pool_id='.$pool)
					 ->where('a.difficulty_level='.$difficulty)
					 ->where('a.topic_id='.$chapter);
					
        $row = $db->fetchAll($select);	      
		return $row;
	}
	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
						  ->from($this->_name);		
		return $selectData;
	}
	
	
	public function addData($data){		
		$id = $this->insert($data);
		return $id;
	}
	
	
	public function updateData($data,$id){		
		$this->update($data, $this->_primary .' =' . (int)$id);
	}
	
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}

}
?>