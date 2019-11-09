<?php
class GeneralSetup_Model_Course extends Zend_Db_Table
{
    protected $_name = "course";
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
    
	public function findreturnselect ($condition=NULL)
    {
       $sql = $this->_db->select()->from($this->_name);
        
       if($condition!=null){
			
			if($condition['keyword']!=""){
				$sql->where("courseid LIKE '%" .$condition['keyword']."%'");
				$sql->orwhere("coursename LIKE '%" .$condition['keyword']."%'");
			}			
			
		}
		
        return $sql;
    }
        
    
     public function returnselect ()
    {
        $sql = $this->_db->select()
                    ->from($this->_name)
                    ->order('id ASC');
       
        return $sql;
    }
    
    public function getCourseByFaculty($fid)
    {
    	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
		
        $sql = $this->_db->select()
        			     ->from(array('c'=>$this->_name));
                         
         if($data->user_role!=1){ //if not system admin view course assigned only
        	$sql->joinLeft(array('cu'=>'course_user'),'cu.courseid = c.courseid');
            $sql->where("cu.username ='".$data->username."'")   ;  
        }
        
        if ($fid != "") {
            $sql->where("c.faculty_id = '".$fid."'");
        }
       
       
        $result = $this->_db->fetchAll($sql);        
        return $result;
    }
    
    public function getCourseByCourseid ($courseid = "")
    {
        $sql = $this->_db->select()->from($this->_name);
        if ($courseid != "") {
            $sql->where("courseid = '".$courseid."'");
        }
      
        $result = $this->_db->fetchRow($sql);     
        return $result;
    }
    
    public function getCourseByDeveloper ($username = "")
    {
        $sql = $this->_db->select()
                         ->from(array('c'=>$this->_name))
                         ->joinLeft(array('cu'=>'course_user'),'cu.courseid = c.courseid')
                         ->where("cu.username ='".$username."'")   ;   
       
        $result = $this->_db->fetchAll($sql);     
        return $result;
    }
   
    
     /* ===========================================================================================
	   Function    : To get course (paginator) with condition
	   Created By  : Mardhiyati Ipin
	   Created On  : 24 March 2012
	   =========================================================================================== */
	
	public function getCourses($condition=NULL){
		
		$select = $this->_db->select()
                          ->from($this->_name)
                          ->order('courseid ASC');
                          
        if($condition!=null){
		    if($condition['course_id']!=""){
				$select->where("course_id = '" .$condition['course_id']."'");
			}
			
			 if($condition['faculty_id']!=""){
				$select->where("faculty_id = '" .$condition['faculty_id']."'");
			}
			
			if($condition['keyword']!=""){
				$select->where("courseid LIKE '%" .$condition['keyword']."%'");
				$select->orwhere("coursename LIKE '%" .$condition['keyword']."%'");
			}
			
			
		}
		
		 $result = $this->_db->fetchAll($select);		 
         
		 return $result;
        
	}
    
}
?>
