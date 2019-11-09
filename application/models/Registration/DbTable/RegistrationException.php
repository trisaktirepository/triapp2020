<?php
class App_Model_Registration_DbTable_RegistrationException extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'registration_exception';
	protected $_primary = "id_paymentex";

	public function getData($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
		->from(array('pe'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = pe.create_by')
		->where("pe.id_paymentex = ?", (int)$id);

		$row = $db->fetchRow($selectData);
		return $row;
	}
	
	public function getDataByStd($nim,$sem){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('pe'=>$this->_name))
		->join(array('sm'=>'tbl_semestermaster'),'pe.idsemester=sm.IdSemesterMaster')
		->where('pe.idreg=?',$nim)
		->where('pe.date_from <= CURDATE() and pe.date_to >=CURDATE()')
		;
		if ($sem!=null) $selectData->where('pe.idsemester=?',$sem);
		$row = $db->fetchAll($selectData);
		return $row;
	}
	
	public function insert(array $data){
	
		$auth = Zend_Auth::getInstance();
	
		if(!isset($data['create_by'])){
			$data['create_by'] = $auth->getIdentity()->iduser;
		}
	
		$data['create_date'] = date('Y-m-d H:i:s');
			
		return parent::insert( $data );
	}
}
?>