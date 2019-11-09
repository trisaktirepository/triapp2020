<?php
class GeneralSetup_Model_Syllabus extends Zend_Db_Table
{
    protected $_name = "syllabus";
    protected $_primary = "id";
    
     
    public function fetchAll ()
    {
        $sql = $this->_db->select()->from($this->_name);
        $stmt = $this->_db->query($sql);
        return $stmt;
    }
   
    public function fetch ($id = "")
    {
        $sql = $this->_db->select()->from($this->_name);
        if ($id != "") {
            $sql->where("id ='".$id."'");
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
    
         
   
    public function getTopicByCourseid ($courseid = "")
    {
        $sql = $this->_db->select()->from($this->_name);
        if ($courseid != "") {
            $sql->where("courseid = '".$courseid."'");
            $sql->where("parent_id=0");
        }
       
        $result = $this->_db->fetchAll($sql);  	   
	    return $result;   
    }
    
    
    
    
    
    
   
}
?>
