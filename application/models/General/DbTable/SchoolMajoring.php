<?php 
class App_Model_General_DbTable_SchoolMajoring extends Zend_Db_Table_Abstract
{
    protected $_name = 'school_majoring';
	protected $_primary = "smj_code";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('s'=>$this->_name))
	                 ->where('s.'.$this->_primary.' = ' .$id);
	                 
					                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from(array('s'=>$this->_name));                 
			
	        $row = $db->fetchAll($select);
	        
		}
		
		return $row;
	}
	
	
	public function getMajoring($condition=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
	                 ->from(array('s'=>$this->_name));
	                 
	       /*if($condition!=null){
	       		if($condition["state_id"]!=''){
	       			$select->where("s.sm_state='".$condition["state_id"]."'");
	       		}
	      	 if($condition["city_id"]!=''){
	       			$select->where("s.sm_city='".$condition["city_id"]."'");
	       		}
	       }*/
	                 
	      $row = $db->fetchAll($select);
	      
	      return $row;            
	}
	
}
?>