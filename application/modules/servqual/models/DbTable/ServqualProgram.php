<?php
class Servqual_Model_DbTable_ServqualProgram extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_program';
	protected $_primary ='id';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'id='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$lobjFormData);
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'id='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq.'=>$this->_name))
				->join(array('pr'=>'tbl_program'),'sq.program_id=pr.IdProgram',array('pr.ProgramName','pr.ArabicName','pr.ProgramCode'));
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('id=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
     
}