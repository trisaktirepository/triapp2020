<?php 
class App_Model_General_DbTable_VenueType extends Zend_Db_Table_Abstract
{
    protected $_name = 'g010_venue_Type';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                ->from(array('vt'=>$this->_name))
	                ->where('vt.'.$this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('vt'=>$this->_name));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
		
		return $row;
	}
}
?>