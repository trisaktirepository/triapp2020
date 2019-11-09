<?php 
class App_Model_General_DbTable_FamilyJob extends Zend_Db_Table_Abstract
{
    protected $_name = 'appl_family_job';
	protected $_primary = "afj_id";
	
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
	
}
?>