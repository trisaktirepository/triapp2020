<?php
class Studentfinance_Model_DbTable_DiscountType extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'discount_type';
	protected $_primary = "dt_id";
		
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('dt'=>$this->_name));
					
		if($id!=null){
			$selectData->where("dt.dt_id = '".$id."'");
			
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
				->from(array('dt'=>$this->_name))
				->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = dt.dt_creator', array())
				->joinLeft(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = tu.IdStaff', array("creator"=>new Zend_Db_Expr("CONCAT_WS(' ', fName,Mname,Lname)")));
	
		return $selectData;
	}
	
	public function insert($data=array()){
		
		if( !isset($data['dt_creator']) ){
			$auth = $auth = Zend_Auth::getInstance();
			
			$data['dt_creator'] = $auth->getIdentity()->iduser; 
		}
		
		if( !isset($data['dt_create_date']) ){
			$data['dt_create_date'] = date('Y-m-d H:i:a'); 
		}
		
		return parent::insert($data);
	}
}

