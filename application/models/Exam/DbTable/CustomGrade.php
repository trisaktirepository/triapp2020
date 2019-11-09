<?php

class App_Model_Exam_DbTable_CustomGrade extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e006_custom_grade';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Custom Grade");
		}			
		return $row->toArray();
	}
	
	
	public function getProgramGrade($program_id,$semester_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		     
		$select = $db->select()
					 ->from($this->_name);
					 
		if ($program_id)  $select->where('program_id = ?',$program_id);  
		if ($semester_id) $select->where('semester_id= ?',$semester_id);   
			      		  $select->order('id');
			      		  
		      		 
		$stmt = $db->query($select);
	    $row = $stmt->fetchAll();
		return $row;
	}
	
	
	public function verifyGrade($id,$verification_id){
		$auth = Zend_Auth::getInstance(); 
		
		//update
		$info = array ('verification_id'=>$verification_id,
					   'modifiedby'     => $auth->getIdentity()->id,
		 			   'modifieddt'     => date("Y-m-d H:i:s")	
					   );		
					   
		$this->update($info, $this->_primary .' = '. (int)$id);
		
	}

 	public function getGradeInfo($program_id,$semester_id)
    {
    	
    	$db = Zend_Db_Table::getDefaultAdapter();	
				        
		$select = $db->select(array('group_id'=>'g.id'))
					    ->from(array('g'=>'e011_grade_group'),array('group_id'=>'g.id'))				       
				        ->join(array('cg'=>'e006_custom_grade'),'g.id = cg.grade_group_id',array())
				        ->where('cg.program_id  = ?',$program_id)
				        ->where('cg.semester_id = ?',$semester_id);

        $select2   = $db->select()
    				->from('e001_grade')									
					->where('grade_group_id = (?)', $select);							

					
           		 
		$stmt = $db->query($select2);
	    $row = $stmt->fetchAll();
		return $row;
    }
    
    
	public function getGroupInfo($program_id,$semester_id)
    {
    	
    	$db = Zend_Db_Table::getDefaultAdapter();	
				        
		$select = $db->select()
					    ->from(array('g'=>'e011_grade_group'))				       
				        ->join(array('cg'=>$this->_name),'g.id = cg.grade_group_id')
				        ->where('cg.program_id  = ?',$program_id)
				        ->where('cg.semester_id = ?',$semester_id);
        
				     
		$row = $db->fetchRow($select);
		return $row;
    }
    
    
    public function insertData($data){
    	$auth = Zend_Auth::getInstance(); 
    	
    	$data["createdby"]  = $auth->getIdentity()->id;
    	$data["createddt"]  = date("Y-m-d H:i:s");
    	$data["modifiedby"] = $auth->getIdentity()->id;
    	$data["modifieddt"] = date("Y-m-d H:i:s");
    	$this->insert($data);
    	
    }
    
    
    
}