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
	
	public function insert(array $data){
		
		if( !isset($data['dcnt_creator']) ){
			$auth = $auth = Zend_Auth::getInstance();
			
			$data['dcnt_creator'] = $auth->getIdentity()->iduser; 
		}
		
		if( !isset($data['dcnt_create_date']) ){
			$data['dcnt_create_date'] = date('Y-m-d H:i:a'); 
		}
		
		return parent::insert($data);
	}
}

