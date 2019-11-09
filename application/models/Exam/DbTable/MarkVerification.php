<?php
class App_Model_Exam_DbTable_MarkVerification extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e009_mark_verification';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Custom Grade");
		}			
		return $row->toArray();
	}
	
	public function verifyMark($request_no){
			$auth = Zend_Auth::getInstance(); 
		
			 $data = array('request_no' => $request_no,
						   'createddt' =>  date("Y-m-d H:i:s"),
						   'createdby' =>  $auth->getIdentity()->id);
			
			$id=$this->insert($data);
			return $id;
			
	}
	
}