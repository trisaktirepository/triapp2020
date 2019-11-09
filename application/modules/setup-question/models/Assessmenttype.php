<?php
class SetupQuestion_Model_Assessmenttype extends Zend_Db_Table
{
    protected $_name = "assessment_component";
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
            $sql->where("id = '".$id."'");
        }
      
        $result = $this->_db->fetchRow($sql);       
        return $result;
    }
    
  
    
     public function findreturnselect ($keyword = "")
    {
        $sql = $this->_db->select()->from($this->_name);
       
        return $sql;
    }
     public function returnselect ()
    {
        $sql = $this->_db->select()
            ->from($this->_name)
            ->order('id ASC');        
        return $sql;
    }
    
    
    public function getMainComponent($course_id){
			
		$select  = $this->_db->select()
	                         ->from($this->_name)
	                         ->where("parent_id='0'");                   
	 
	    if($course_id) $select->where("courseid = '".$course_id."'");	
	    
	    echo $select;
	   
	    $result = $this->_db->fetchAll($select);
	  
	    return $result;   
	}
	
	public function getComponentItem($parent_id){
			
		$select  = $this->_db->select()
	                         ->from($this->_name);
	                    
	    if($parent_id)$select->where("parent_id = '".$parent_id."'");	 

	    echo $select;
	
	    $result = $this->_db->fetchAll($select);  	   
	    return $result;   
	}
	
	 public function getComponentName ($id = "")
    {
        $sql = $this->_db->select()
                         ->from($this->_name)
                         ->where("id = '".$id."'");        
     
         $child = $this->_db->fetchRow($sql);  
        
         $sql_parent = $this->_db->select()
                         ->from($this->_name)
                         ->where("id = '".$child["parent_id"]."'");        
     
        $parent = $this->_db->fetchRow($sql_parent);      
                
        return $parent["component_name"].' - '.$child["component_name"];
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
    
    
    
    
   
}
?>
