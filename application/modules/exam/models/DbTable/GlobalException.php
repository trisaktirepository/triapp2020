<?php
class Exam_Model_DbTable_GlobalException extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'global_payment_exception';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('a'=>$this->_name))
							->where('a.'.$this->_primary .' = ?', $id);
			
			$row = $db->fetchRow($select);
		}else{
			$row = $this->fetchAll();
			
			$row = $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is no data");
		}
			
		return $row;
	}
	
	public function isException($iduniv,$idcollege,$idsem,$idtype){
	 
			$db = Zend_Db_Table::getDefaultAdapter();
				
			$select = $db->select()
			->from(array('a'=>$this->_name))
			->where('a.id_univ=?',$iduniv)
			->where('a.idsemestermain=?',$idsem)
			->where('a.exception_type=?',$idtype)
			->where('a.id_faculty=?',$idcollege);
				
			$row = $db->fetchRow($select);
			if (!$row){
				$select = $db->select()
				->from(array('a'=>$this->_name))
				->where('a.id_univ=?',$iduniv)
				->where('a.idsemestermain=?',$idsem)
				->where('a.exception_type=?',$idtype)
				->where('a.id_faculty is null');
				$row = $db->fetchRow($select);
			}
		//echo $select;exit;
		return $row;
	}
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('a'=>$this->_name));
		
		return $select;
	}
}

