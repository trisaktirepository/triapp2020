<?php 
class App_Model_General_DbTable_SchoolMaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'school_master';
	protected $_primary = "sm_id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			return null;
		}else{
			return $row->toArray();	
		}
	}
	
	public function getSchool($condition=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
	                 ->from(array('s'=>$this->_name));
	                 
	       if($condition!=null){
	       		if($condition["state_id"]!=''){
	       			$select->where("s.sm_state='".$condition["state_id"]."'");
	       		}
	      	 if($condition["city_id"]!=''){
	       			$select->where("s.sm_city='".$condition["city_id"]."'");
	       		}
	       }
	                 
	      $row = $db->fetchAll($select);
	      
	      return $row;            
	}
	
}
?>