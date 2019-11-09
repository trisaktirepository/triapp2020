<?php


class App_Model_Exam_DbTable_Asscompitem extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e012_course_mark_component_item';
	protected $_primary = "id";
		
	public function getAsscomponentitem($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		
		if(!$row){
			throw new Exception("There is No Assessment Component Item");
		}	
		//print_r($row->toArray());		
		return $row->toArray();
	}
	
		
	public function addAsscomponentitem($data){
	
		$this->insert($data);
	}
	
	public function updateAsscomponentitem($data,$id){
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteAsscomponentitem($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
	
	public function getCompitemByCompId($component_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select  = $db->select()
	                    ->from(array('cmci'=>$this->_name))
	                    ->joinLeft(array('c'=>'e014_assessment_component'),'c.id = cmci.component_item_id',array('component_name'=>'c.component_name'));	                     
	   
	    if ($component_id)   $select->where('cmci.component_id= ?',$component_id);   
					         $select->order('cmci.id');	
		 //echo $select;
		
        $result = $db->fetchAll($select);  
		return $result;
	}
	
   public function getSumCompitemByCompId($component_id){
		
		$select  = $this->select()
	                     ->from($this->_name,array('SUM(component_item_weightage) as total_item_weightage'));	                     
	   
	    if ($component_id)   $select->where('component_id= ?',$component_id);   
					         $select->order('id');	
					     
        $result = $this->fetchRow($select);  
       
		return $result->toArray();
	}
	
	public function getData($component_id,$component_item_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select  = $db->select()
	                   ->from(array('cmci'=>$this->_name))                   
	                   ->order('cmci.id');
	   
	    if ($component_id)        $select->where('cmci.component_id= ?',$component_id);   
	    if ($component_item_id)   $select->where('cmci.component_item_id= ?',$component_item_id); 
	  
	   // echo $select;
        $result = $db->fetchRow($select);  
		return $result;
	}
}

