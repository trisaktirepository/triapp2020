<?php
class Studentfinance_Model_DbTable_FeeItemAccount extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_item_account';
	protected $_primary = "fiacc_id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fia'=>$this->_name));
		
		if($id!=0){
			$selectData->where("fia.fiacc_id = '".$id."'");
			
			$row = $db->fetchRow($selectData);
		}else{
			
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}				
		
	}
	
	public function getFeeItem($faculty_id, $program_id, $fee_item_id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fi'=>'fee_item'))
					->joinLeft(array('fia'=>$this->_name),'fia.fiacc_faculty_id = '.$faculty_id.' and fia.fiacc_program_id = '.$program_id.' and fia.fiacc_fee_item = fi.fi_id')
					//->joinLeft(array('tdf'=>'tbl_definationms'), 'tdf.idDefinition = fia.fiacc_bank', array('bank_name'=>'BahasaIndonesia'));
					//->where('fia.fiacc_fee_item =?', $fee_item_id);
					->joinLeft(array('tdf'=>'tbl_bank'), 'tdf.IdBank = fia.fiacc_bank', array('bank_name'=>'BankName'));
		if($fee_item_id!=null){
			$selectData->where('fia.fiacc_fee_item =?', $fee_item_id);
			$row = $db->fetchRow($selectData);
		}else{
			$selectData;
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}
	}
	
	public function getFacultyData($fee_item_id, $faculty_id=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('c'=>'tbl_collegemaster'))
					->joinLeft(array('fia'=>$this->_name),'fia.fiacc_faculty_id = c.IdCollege and fia.fiacc_fee_item = '.$fee_item_id)
					->joinLeft(array('tdf'=>'tbl_definationms'), 'tdf.idDefinition = fia.fiacc_bank', array('bank_name'=>'BahasaIndonesia'));
					//->where('fia.fiacc_fee_item =?', $fee_item_id);
		
		if($faculty_id!=null){
			$selectData->where("fia.fiacc_faculty_id = ?",$faculty_id);
			
			$row = $db->fetchRow($selectData);
		}else{

			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}
	}
		
	public function insert(array $data){
		
		$auth = Zend_Auth::getInstance();
		
		$data['fiacc_creator'] = $auth->getIdentity()->iduser;
		$data['fiacc_create_date'] = date('Y-m-d H:i:s');
			
        return parent::insert($data);
	}

	public function update(array $data, $condition){
		
		$auth = Zend_Auth::getInstance();
		
		$data['fiacc_update_by'] = $auth->getIdentity()->iduser;
		$data['fiacc_update_date'] = date('Y-m-d H:i:s');
			
        return parent::update($data,$condition);
	}

	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'status' => 0				
			);
				
			$this->update($data, "id = '".$id."'");
		}
	}	
}

