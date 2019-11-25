<?php

class Application_Model_DbTable_ProformaInvoiceDetail extends Zend_Db_Table {

	protected $_name = 'proforma_invoice_detail';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
  	}

	public function getData($id=""){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->order("id desc");
					  
		if($id)	{			
			 $select->where("id ='".$id."'");
			 $row = $db->fetchRow($select);				 
		}else{
			 $row = $db->fetchAll($select);			
		}	 
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	public function isIn($idinvoice,$fiid){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from($this->_name)
		->where('fi_id=?',$fiid)
		->where("invoice_main_id =?",$idinvoice);
		return $db->fetchRow($select);
	
	}
	
	public function getInvoiceDetailBank($invoice_id,$program){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('idtl'=>$this->_name),array('amount'))
		->join(array('item'=>'fee_item'),'item.fi_id=idtl.fi_id',array('bni_code','fi_code'))
		->join(array('ia'=>'fee_item_account'),'item.fi_id=ia.fiacc_fee_item',array())
		->join(array('acc'=>'tbl_bank_account'),'ia.fiacc_account=acc.account',array('account_code'))
		->where("idtl.invoice_main_id = ?", $invoice_id)
		->where('ia.fiacc_program_id=?',$program);
	
		$row = $db->fetchAll($selectData);
	
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	public function getDataByInvoice($idinvoice){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from(array('idtl'=>$this->_name))  
		->where("invoice_main_id =?",$idinvoice);
		return $db->fetchRow($select);
	
	}
	
	public function updateData($data,$id){
		$id = $this->update($data,'id='.$id);
		return $id;
	}
	
	public function addData($data){
		$id = $this->insert($data);
		return $id;
	}
	
	 
	
	 
}