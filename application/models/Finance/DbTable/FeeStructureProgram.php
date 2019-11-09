<?php 
class App_Model_Finance_DbTable_FeeStructureProgram extends Zend_Db_Table_Abstract {

	protected $_name = 'fee_structure_program';
	protected $_primary = "fsp_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.IdProgram = fsp.fsp_program_id')
					->where("fsp.fsp_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getStructureData($fee_structure_id, $program_id = 0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.IdProgram = fsp.fsp_program_id')
					->where("fsp.fsp_fs_id = '".$fee_structure_id."'");
					
		if($program_id!=0){
			$selectData->where('fsp.fsp_program_id = '.$program_id);
			$row = $db->fetchRow($selectData);
		}else{
			$row = $db->fetchAll($selectData);	
		}			

		if($row){
			return $row;
		}else{
			return null;
		}	
	}
}

?>