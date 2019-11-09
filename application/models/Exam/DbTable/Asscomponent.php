<?php

class App_Model_Exam_DbTable_Asscomponent extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e002_course_mark_component';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		
		if(!$row){
			throw new Exception("There is No Assessment Component");
		}			
		return $row->toArray();
	}
	
	public function getInfo($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
			
	   $select  = $db->select()
	                    ->from(array('cmc'=>$this->_name))
	                    ->join(array('c'=>'e014_assessment_component'),'c.id = cmc.component_id',array('component_name'=>'c.component_name'));	                     
	   
	    if ($id)  $select->where('cmc.id= ?',$id);   
	   					     
        $result = $db->fetchRow($select); 
      
      	return $result;		
		
	}
	
	
	
	
		
	public function addAsscomponent($data){		
			
		$this->insert($data);
	}
	
	public function updateAsscomponent($data,$id){
		
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteAsscomponent($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
	public function getAssComponent($program_id,$course_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
			
	    $select  = $db->select()
	                    ->from(array('cmc'=>$this->_name))
	                    ->join(array('c'=>'e014_assessment_component'),'c.id = cmc.component_id',array('component_name'=>'c.component_name'));	                     
	   
	    if ($program_id)  $select->where('cmc.program_id= ?',$program_id);   
	    if ($course_id)   $select->where('cmc.course_id= ?',$course_id);   
					      $select->order('cmc.id');	
					     
        $result = $db->fetchAll($select); 
      
      	return $result;		
		
	}
	
	public function getComponentList($program_id,$course_id){
			
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		$select  = $db->select()
	                    ->from(array('cmc'=>$this->_name))
	                    ->join(array('c'=>'e014_assessment_component'),'c.id = cmc.component_id',array('component_name'=>'c.component_name'));
	                    
	    if($program_id)$select->where('cmc.program_id = '.$program_id);
	    if($course_id) $select->where('cmc.course_id = '.$course_id);
	    
	    //echo $select;
	  
	    $result = $db->fetchAll($select);  
	  
	    return $result;   
	}
	
	
}
?>