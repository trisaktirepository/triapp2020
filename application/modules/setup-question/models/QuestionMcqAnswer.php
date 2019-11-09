<?php
class Question_Model_QuestionMcqAnswer extends Zend_Db_Table
{
    protected $_name = "question_mcq_answer";
    
     
    public function fetchAll ()
    {
        $sql  = $this->_db->select()->from($this->_name);
        $stmt = $this->_db->query($sql);
        return $stmt;
    }
   
    public function fetch ($id = "")
    {
        $sql = $this->_db->select()->from($this->_name);
        if ($id != "") {
            $sql->where('id = ?', $id);
        }
        $result = $this->_db->fetchRow($sql);      
        return $result;
    }    
    
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
	
	/* =======================================================++====
	   Function    : To preview question
	   Created By  : Mardhiyati Ipin
	   Created On  : 16 March 2012
	   ===================================================++======== */
	
	public function preview($qmid=''){		
		
		$select = $this->_db->select()
                          ->from($this->_name)                          
                          ->where("question_main_id='".$qmid."'");
                    
	    $result = $this->_db->query($select);
      
        return $result;
				
	}
	
	public function getQuestion ($qmid=''){		
		
		$select = $this->_db->select()
                          ->from($this->_name)                          
                          ->where("question_main_id='".$qmid."'");
                    
	    $result = $this->_db->fetchAll($select);   
      
        return $result;
				
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
