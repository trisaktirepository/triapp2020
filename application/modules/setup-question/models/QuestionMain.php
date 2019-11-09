<?php
class Question_Model_QuestionMain extends Zend_Db_Table
{
    protected $_name = "question_main";
    
     
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
    
    public function modify ($data, $id)
    {
        $this->_db->update($this->_name, $data, 'id = ' . (int) $id);
    }
    
    public function delete ($id)
    {
        $this->_db->delete($this->_name, 'id = ' . (int) $id);
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
    
    public function addData($data){				
		$id = $this->insert($data);
		return $id;
	}
	
	public function updateData($data,$id){			
		$this->_db->update($this->_name,$data,"id = '".$id."'");
	}
	
	public function deleteData($id){		
		$this->_db->delete($this->_name," id = '" . (int)$id."'");
	}
	
	/* =======================================================++====
	   Function    : To display questions no condition (paginator)
	   Created By  : Mardhiyati Ipin
	   Created On  : 15 March 2012
	   ===================================================++======== */
   
	public function viewAll(){
		
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
		
		$select = $this->_db->select()
                          ->from(array('qm'=> $this->_name))
                          ->join(array('q'=> 'question'),'qm.`id` = q.question_main_id',array('qid'=>'id','question'=>'question','answer'=>'answer'))
                          ->join(array('s'=> 'status'),'s.`id` = qm.status',array('status_name'=>'s.name'))
                          ->joinLeft(array('sy'=>'syllabus'),'sy.id = qm.topic_id',array('topic_name'=>'sy.name'))
                          ->joinLeft(array('tl'=> 'taxanomy_level'),'tl.`id` = qm.taxanomy_level',array('taxanomy_name'=>'tl.taxanomy_name'))
                          ->where('q.language=1');                                                
                         
                          
        if($condition['orderby']!=""){
			$select->order('qm.'.$condition['orderby'].' ASC');
		}else{
			$select->order('qm.id ASC');
		}  

		//echo $select;
		//view as developer :: where the developer cannot see questions created by others.   
        if($data->user_role==3){
         	$select->where("qm.createdby='".$data->username."'");
        }
             
		return $select;
				
	}
	
	
	/* =======================================================++====
	   Function    : To display questions with condition (paginator)
	   Created By  : Mardhiyati Ipin
	   Created On  : 15 March 2012
	   ===================================================++======== */
   
	public function view($condition=NULL){
		
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        
		$select = $this->_db->select()
                          ->from(array('qm'=> $this->_name))                         
                          ->join(array('q'=> 'question'),'qm.`id` = q.question_main_id',array('qid'=>'id','question'=>'question','answer'=>'answer'))
                          ->joinLeft(array('s'=> 'status'),'s.`id` = qm.status',array('status_name'=>'s.name'))
                          ->joinLeft(array('sy'=>'syllabus'),'sy.id = qm.topic_id',array('topic_name'=>'sy.name'))
                          ->joinLeft(array('tl'=> 'taxanomy_level'),'tl.`id` = qm.taxanomy_level',array('taxanomy_name'=>'tl.taxanomy_name'))
                          ->where('q.language=1');

	                           
			    
        if($condition!=null){
		     if($condition['course_id']!=""){
				$select->where("qm.course_id = '" .$condition['course_id']."'");
			}
			
			 if($condition['topic_id']!=""){
				$select->where("qm.topic_id = '" .$condition['topic_id']."'");
			}
			
			if($condition['createdby']!=""){
				$select->where("qm.createdby = '" .$condition['createdby']."'");
			}
			
			if($condition['status']!=""){
				$select->where("qm.status = '" .$condition['status']."'");
			}
			
			if($condition['question']!=""){
				$select->where("q.question LIKE '%" .$condition['question']."%'");
			}			
		}	
		
		if($condition['orderby']!=""){
			$select->order('qm.'.$condition['orderby'].' ASC');
		}else{
			$select->order('qm.id ASC');
		}   
		
		//view as developer :: where the developer cannot see questions created by others.
		if($data->user_role==3){
         	$select->where("qm.createdby='".$data->username."'");
        }
     //  echo $select;  
          
		return $select;
				
	}
	
	
	/* =======================================================++====
	   Function    : To preview question
	   Created By  : Mardhiyati Ipin
	   Created On  : 16 March 2012
	   ===================================================++======== */
	
	public function preview($qmid=''){
		
		
		$select = $this->_db->select()
                          ->from(array('qm'=> $this->_name))                          
                          ->joinLeft(array('tl'=> 'taxanomy_level'),'tl.`id` = qm.taxanomy_level',array('taxanomy_name'=>'tl.taxanomy_name'))
                          ->joinLeft(array('s'=> 'status'),'s.`id` = qm.status',array('status_name'=>'s.name'))
                          ->joinLeft(array('sy'=>'syllabus'),'sy.id = qm.topic_id',array('topic_name'=>'sy.name'))
                          ->where("qm.id='".$qmid."'");
                          
		$result = $this->_db->fetchRow($select);
      
        return $result;
				
	}
	
		
	/* ===========================================================================================
	   Function    : To count total no of question for each topic and status.
	   Created By  : Mardhiyati Ipin
	   Created On  : 15 March 2012
	   =========================================================================================== */
	
	public function getData($condition=NULL){
		
		$select = $this->_db->select()
                          ->from($this->_name,'COUNT(*) AS total')
                          ->order('id ASC');
                          
        if($condition!=null){
        	if($condition['createdby']!=""){
				$select->where("createdby = '" .$condition['createdby']."'");
			}
			
		    if($condition['course_id']!=""){
				$select->where("course_id = '" .$condition['course_id']."'");
			}
			
			 if($condition['topic_id']!=""){
				$select->where("topic_id = '" .$condition['topic_id']."'");
			}
			
			if($condition['taxanomy_level']!=""){
				$select->where("taxanomy_level = '" .$condition['taxanomy_level']."'");
			}
			
			if($condition['compid']!=""){
				$select->where("assessment_type = '" .$condition['compid']."'");
			}
			
			 if($condition['status']!=""){
				$select->where("status = '" .$condition['status']."'");
			}
		}
		
		 $result = $this->_db->fetchRow($select);		 
       //  ECHO $select;
		 return $result["total"];
        
	}
	
	
	/* =======================================================++====================
	   Function    : To display questions with condition (paginator)(MODERATOR VIEW)
	   Created By  : Mardhiyati Ipin
	   Created On  : 21  March 2012
	   ===================================================++======================== */
   
	public function moderatorView($condition=NULL){
		
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        
		$select = $this->_db->select()
                          ->from(array('qm'=> $this->_name))                         
                          ->join(array('q'=> 'question'),'qm.`id` = q.question_main_id',array('qid'=>'id','question'=>'question','answer'=>'answer'))
                          ->joinLeft(array('s'=> 'status'),'s.`id` = qm.status',array('status_name'=>'s.name'))
                          ->joinLeft(array('sy'=>'syllabus'),'sy.id = qm.topic_id',array('topic_name'=>'sy.name'))
                          ->joinLeft(array('tl'=> 'taxanomy_level'),'tl.`id` = qm.taxanomy_level',array('taxanomy_name'=>'tl.taxanomy_name'))
                          ->where('q.language=1');

	                           
			    
        if($condition!=null){
		     if($condition['course_id']!=""){
				$select->where("qm.course_id = '" .$condition['course_id']."'");
			}
			
			 if($condition['topic_id']!=""){
				$select->where("qm.topic_id = '" .$condition['topic_id']."'");
			}
			
			if($condition['createdby']!=""){
				$select->where("qm.createdby = '" .$condition['createdby']."'");
			}
			
			if($condition['status']!=""){
				$select->where("qm.status = '" .$condition['status']."'");
			}
			
			if($condition['question']!=""){
				$select->where("q.question LIKE '%" .$condition['question']."%'");
			}		
		
		}	
		
		if($condition['orderby']!=""){
			$select->order('qm.'.$condition['orderby'].' ASC');
		}else{
			$select->order('qm.id ASC');
		}   	
         
         
		return $select;
				
	}
	
	
	
	
	/* =======================================================++====
	   Function    : To display questions with condition (no paginator)
	   Created By  : Mardhiyati Ipin
	   Created On  : 1 April 2012
	   ===================================================++======== */
   
	public function viewprint($condition=NULL){
		
		$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        
		$select = $this->_db->select()
                          ->from(array('qm'=> $this->_name))                         
                          ->join(array('q'=> 'question'),'qm.`id` = q.question_main_id',array('qid'=>'id','question'=>'question','answer'=>'answer'))
                          ->joinLeft(array('s'=> 'status'),'s.`id` = qm.status',array('status_name'=>'s.name'))
                          ->joinLeft(array('sy'=>'syllabus'),'sy.id = qm.topic_id',array('topic_name'=>'sy.name','topic_level'=>'sy.level'))
                          ->joinLeft(array('tl'=> 'taxanomy_level'),'tl.`id` = qm.taxanomy_level',array('taxanomy_name'=>'tl.taxanomy_name'))
                          ->where('q.language=1');

	                           
			    
        if($condition!=null){
		     if($condition['course_id']!=""){
				$select->where("qm.course_id = '" .$condition['course_id']."'");
			}
			
			 if($condition['topic_id']!=""){
				$select->where("qm.topic_id = '" .$condition['topic_id']."'");
			}
			
			if($condition['createdby']!=""){
				$select->where("qm.createdby = '" .$condition['createdby']."'");
			}
			
			if($condition['status']!=""){
				$select->where("qm.status = '" .$condition['status']."'");
			}
			
			if($condition['component_id']!=""){
				$select->where("qm.component_id = '" .$condition['component_id']."'");
			}
			
			if($condition['question']!=""){
				$select->where("q.question LIKE '%" .$condition['question']."%'");
			}			
		}	
		
		if($condition['orderby']!=""){
			if($condition['orderby']=='level')
				$select->order('sy.'.$condition['orderby'].' ASC');
			else 
			    $select->order('qm.'.$condition['orderby'].' ASC');
			
		}else{
			$select->order('qm.id ASC');
		}   
		
		
     //  if($data->username=="mardhiyati") echo $select;  
        
        $result = $this->_db->fetchAll($select);
        
		return $result;
				
	}
	
	
	
	
	
	
	
   
}
?>
