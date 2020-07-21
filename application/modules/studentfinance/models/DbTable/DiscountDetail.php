<?php
class Studentfinance_Model_DbTable_DiscountDetail extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'discount_detail';
	protected $_primary = "dcntdtl_id";
		
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('d'=>$this->_name));
					
		if($id!=null){
			$selectData->where("d.dcntdtl_id = '".$id."'");
			
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
	
	public function isIn($iddisc,$fiid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->where('d.dcntdtl_fi_id=?',$fiid)
		->where('d.dcnt_id=?',$iddisc);
		 
		$row = $db->fetchRow($selectData);
		 
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	 
	
	public function getDataByMain($main){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->where('d.dcnt_id =?',$main);
			
		$row = $db->fetchAll($selectData);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function insert(array $data){
		
		 
		
		return parent::insert($data);
	}
}

