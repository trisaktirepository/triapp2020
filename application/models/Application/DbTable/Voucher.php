<?php

class App_Model_Application_DbTable_Voucher extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_voucher';
	protected $_primary = "idvoucher";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('idvoucher=?',$id);
						
			$row = $db->fetchAll($select);
		 
		 
		
		return $row;
	}
	
	public function getDataByVoucher($voucher,$telp=null){
	 
	
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('v_code=?',$voucher)
		 
		->where('status is null');
		
		if ($telp!=null) $select->where('phone=?',$telp);
	
		$row = $db->fetchRow($select);
		//echo $select;exit;
			
	
		return $row;
	}
	
	 
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('a'=>$this->_name));
		
		return $selectData;
	}
	
	public function addData($postData){

		$this->insert($postData);
 		$id = $this->getAdapter()->lastInsertId();
		return $id;
	}
	
	public function updateData($postData,$id){
		
		 
		
		$this->update($postData, $this->_primary .' = '. (int)$id);
	}
	
	 
	
	
}

