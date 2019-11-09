<?php
class Accessbni_Model_DbTable_VA extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_virtual_acount';
	protected $_primary = "id";
		
	 
	public function insert(array $data){
			
        return parent::insert($data);
	}		
		

	public function updateData(array $data,$where){
		
		 	
		return parent::update($data, $where);
	}
	
	public function deleteData($id=null){
		if($id!=null){
			 
			$this->delete( "id = '".$id."'");
		}
	}	
	
	public function getDataByVA($va,$pnb,$amount,$dtpay){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where('im.virtual_account = ?', $va)
		->where('im.payment_ntb=?',$pnb)
		->where('im.payment_amount=?',$amount)
		->where('im.datetime_payment=?',$dtpay);
		 
		$row = $db->fetchRow($selectData);
		
		//invoice data
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function isInVA($va,$pnb,$amount){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('im'=>$this->_name))
		->where('im.virtual_account = ?', $va)
		->where('im.payment_ntb=?',$pnb)
		->where('im.payment_amount=?',$amount) ;
			
		$row = $db->fetchRow($selectData);
	
		//invoice data
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
}

