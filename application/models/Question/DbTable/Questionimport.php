<?php 
class App_Model_Question_DbTable_Questionimport extends Zend_Db_Table_Abstract
{
    protected $_main = 'q003_question_main';
    protected $_question = 'q004_question';
	protected $_primarymain = "id";
	protected $_primaryquestion = "id";
	
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
	
	public function getQuestionSachin($pool){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 echo $select = $db->select()
		             ->from(array('s'=>'tbl_questions'))	
//		             ->joinLeft(array('q'=>$this->_question),'q.question_main_id = s.id',array('question'=>'q.question','idQuestion'=>'q.id'))
					 ->where('s.QuestionGroup = "'.$pool.'"');
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	public function getanswerSachin($idQuestion){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'tbl_answers'))	
//		             ->joinLeft(array('q'=>$this->_question),'q.question_main_id = s.id',array('question'=>'q.question','idQuestion'=>'q.id'))
					 ->where('s.idquestion = '.$idQuestion);
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
public function getCorrectanswerSachin($idQuestion,$idAnswer){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'tbl_answers'))	
//		             ->joinLeft(array('q'=>$this->_question),'q.question_main_id = s.id',array('question'=>'q.question','idQuestion'=>'q.id'))
					 ->where('s.idanswers = '.$idAnswer)
		             ->where('s.CorrectAnswer = 1');
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	public function getChapter($name){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s'=>'q002_chapter'))	
					 ->where('s.name = "'.$name.'"');
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	public function gettossachin(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 echo $select = $db->select()
		             ->from(array('a'=>'tbl_tosdetail'))	
		             ->joinLeft(array('b'=>'tbl_tossubdetail'),'b.IdTOSDetail = a.IdTOSDetail',array('*'));
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	public function gettos($course){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 echo $select = $db->select()
		             ->from(array('a'=>'q007_tos_main'))	
		             ->joinLeft(array('b'=>'q008_tos_submain'),'b.idTos = a.id',array('idSub'=>'b.id','NosOfQues'=>'b.NosOfQues','pool_id'=>'b.pool_id'))
		             ->joinLeft(array('c'=>'q009_tos_details'),'c.idSubMain = b.id',array('pool_id'=>'c.pool_id','idChapter'=>'c.idChapter','difficulty'=>'c.difficulty','NosOfQuestion'=>'c.NosOfQuestion'))
					->where('a.idCourse ='.$course)
					->where('a.status =1')
					;
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from($this->_name);
		
		return $selectData;
	}
	
	public function addData($table,$data){
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($table,$data);
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