<?php 
class App_Model_Finance_DbTable_FeeStructurePlan extends Zend_Db_Table_Abstract {

	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_plan';
	protected $_primary = "fsp_id";
		
	public function getBillingPlanByPackage($fee_structure_id,$paket){
		$db = Zend_Db_Table::getDefaultAdapter();
		if ($paket=="A") $paket=0; else $paket=1;
		$selectData = $db->select()
		->from(array('fsp'=>$this->_name))
		->where("fsp.fsp_structure_id = '".$fee_structure_id."'")
		->where('fsp.fsp_billing_no = '.$paket );
	
		$row = $db->fetchRow($selectData);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	public function getStructureData($fee_structure_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->where("fsp.fsp_structure_id = '".$fee_structure_id."'");
			
		$row = $db->fetchAll($selectData);	

		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
public function getStructurePlan($fs_id,$fsp_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->where("fsp.fsp_id = '".$fsp_id."'")
					->where("fsp.fsp_structure_id = '".$fs_id."'");
			
		$row = $db->fetchRow($selectData);	

		if($row){
			return $row;
		}else{
			return null;
		}
	}
}

?>