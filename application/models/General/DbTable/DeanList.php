<?php 
class App_Model_General_DbTable_DeanList extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_deanlist';
	protected $_primary = "IdDeanList";
	
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
	
	
	public function getDeanByCollege($idCollege=0){
		
		$idCollege = (int)$idCollege;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('d'=>$this->_name))
	                ->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff=d.IdStaff',array('FullName'=>'s.FullName'))
	                ->where("d.IdCollege='".$idCollege."'");			                     
        
        $row = $db->fetchRow($select);
		return $row;
	}
	
	
}
?>