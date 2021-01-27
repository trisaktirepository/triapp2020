<?php
class Studentfinance_Model_DbTable_Discount extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'discount';
	protected $_primary = "dcnt_id";
		
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('d'=>$this->_name));
					
		if($id!=null){
			$selectData->where("d.dcnt_id = '".$id."'");
			
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
	
	public function isIn($trxid,$invoice,$srkr){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->where('d.dcnt_txn_id=?',$trxid)
		->where('d.dcnt_invoice_id=?',$invoice)
		->where('d.dcnt_letter_number=?',$srkr);
		$row = $db->fetchRow($selectData);
		 	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
				->from(array('d'=>$this->_name))
				->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = d.dcnt_creator', array())
				->joinLeft(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = tu.IdStaff', array("creator"=>new Zend_Db_Expr("CONCAT_WS(' ', fName,Mname,Lname)")));
	
		return $selectData;
	}
	
	public function getDiscountData($fomulir){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('d'=>$this->_name))
					->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = d.dcnt_creator', array())
					->joinLeft(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = tu.IdStaff', array("creator"=>'Fullname'))
					->where('d.dcnt_fomulir_id =?',$fomulir);
					
		$row = $db->fetchAll($selectData);

		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getDataByInvoice($invoice){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->join(array('a'=>'discount_type'),'d.dcnt_type_id=a.dt_id')
		->where('d.dcnt_invoice_id =?',$invoice);
			
		$row = $db->fetchAll($selectData);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getDataFeeItemByInvoice($invoice){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('d'=>$this->_name),array('dcnt_letter_number','dcnt_appl_id'))
		->join(array('a'=>'discount_type'),'d.dcnt_type_id=a.dt_id',array('dt_discount'))
		->join(array('b'=>'discount_detal'),'b.dcnt_id=d.dcnt_id',array('amount'=>'dcntdtl_amount','fi_id'=>'dcntdtl_fi_id','fi_name'=>'dcntdtl_fi_name'))
		->where('d.dcnt_invoice_id =?',$invoice)
		->order('b.dcntdtl_fi_id')
		->order('b.dcntdtl_amount DESC');
			
		$row = $db->fetchAll($selectData);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	 
	public function insert(array $data){
		
		if( !isset($data['dcnt_creator']) ){
			$auth = $auth = Zend_Auth::getInstance();
			
			$data['dcnt_creator'] = $auth->getIdentity()->appl_id; 
		}
		
		if( !isset($data['dcnt_create_date']) ){
			$data['dcnt_create_date'] = date('Y-m-d H:i:a'); 
		}
		
		return parent::insert($data);
	}
}

