<?php
class Studentfinance_Model_DbTable_CreditNoteDetail extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'credit_note_detail';
	protected $_primary = "cnd_id";
		
	public function getCreditNoteDetail($cn_id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
					->from(array('cnd'=>$this->_name))
					->where("cnd.cnd_cn_id = '".$cn_id."'");
		
		$row = $db->fetchAll($selectData);
		
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}				
		
	}
	public function updateData(array $data,$where){
	 
		return parent::update($data, $where);
	}
	public function isIn($cn_id,$fiid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('cnd'=>$this->_name))
		->where("cnd.cnd_cn_id = '".$cn_id."'")
		->where('cnd.cnd_fi_id=?',$fiid);
	
		$row = $db->fetchRow($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function addData(array $data){
	
		$auth = Zend_Auth::getInstance();
	 
		return parent::insert($data);
	}
	
	public function getCreditNoteDetailFromInvoice($invoice_id,$fee_item_id=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('cnd'=>$this->_name))
		->join(array('cn'=>'credit_note'), 'cnd.cnd_cn_id = cn.cn_id', array())
		->join(array('inv'=>'invoice_main'), 'inv.bill_number = cn.cn_billing_no', array())
		->where("inv.id = ?", $invoice_id);
		
		if($fee_item_id){
			$selectData->where('cnd.cnd_fi_id =?',$fee_item_id);
		}
	
		$row = $db->fetchAll($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
		
}

