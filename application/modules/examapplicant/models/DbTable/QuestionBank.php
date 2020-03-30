<?php
class Examapplicant_Model_DbTable_QuestionBank extends Zend_Db_Table_Abstract
{
	
    protected $_name = "tbl_question_bank";
    protected $_primary="idQuestion";
    
     
    
    
    public function addData($data){				
		$id = $this->insert($data);
		return $id;
	}
	
	public function updateData($data,$id){		
		$this->_db->update($this->_name,$data,'id = ' . (int)$id);
	}
	
	public function deleteData($id){		
		$this->_db->delete($this->_name,$this->_primary . ' = ' . (int)$id);
	}
	
	 
	public function preview($qmid=''){		
		
		$select = $this->_db->select()
                          ->from($this->_name)                          
                          ->where("question_main_id='".$qmid."'");
                    
	    $result = $this->_db->query($select);
      
        return $result;
				
	}
	
	public function getQuestion ($id=''){		
		
		
		$select = $this->_db->select()
                          ->from(array('a'=>$this->_name) )  
                          ->join(array('b'=>'appl_component'),'a.subject=b.ac_id')                       
                          ;
		if ($id!=null) {
			$select->where("idQuestion='".$id."'");
			$result = $this->_db->fetchRow($select);
		}
	    else $result = $this->_db->fetchAll($select);   
      
        return $result;
				
	}
	
	public function getParent(){
	
	
		$select = $this->_db->select()
		->from(array('a'=>$this->_name) )
		->join(array('b'=>'appl_component'),'a.subject=b.ac_id')
		->where('a.parent="1"')
		;
		 
		$result = $this->_db->fetchAll($select);
	
		return $result;
	
	}
	
	public function isQSetIn($idSet){
	
	
		$select = $this->_db->select()
		->from(array('a'=>$this->_name) ) 
		->where('a.from_setcode=?',$idSet)
		;
			
		$result = $this->_db->fetchAll($select);
	
		return $result;
	
	}
	
	public function getQuestionBySubject ($subject=null){
	
	
		$select = $this->_db->select()
		->from(array('a'=>$this->_name) )
		->join(array('b'=>'appl_component'),'a.subject=b.ac_id')
		;
		if ($subject!=null)  {
			$select->where("subject='".$subject."'");
		 
			$result = $this->_db->fetchAll($select);
	
			return $result;
		} else return array();
	
	}
	
	public function getAnswers ($qid=''){		
		
		$select = $this->_db->select()
                          ->from($this->_name)                          
                          ->where("question_id='".$qid."'");
                    
	    $result = $this->_db->fetchAll($select);   
       // echo $select;
        return $result;
				
	}
	
	
	
}
?>
