<?php
class Servqual_Model_DbTable_ServqualQuestion extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_servqual_questions';
	protected $_primary ='IdServqualQuestion';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdServqualQuestion='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$lobjFormData);
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqualQuestion='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null,$order=null,$scale=null,$idservqualdetail=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.Category=def.IdDefinition',array('Category'=>'def.BahasaIndonesia'))
				 //->where('sq.active="1"');
				->order('sq.active DESC');
		
		if ($idserqual!=null) {
			$select->where('IdServqualQuestion=?',$idserqual);
		}
		//echo $idserqual;
		if ($idserqual!=null) {
			$row=$db->fetchRow($select);
			//echo var_dump($row);exit;
			if ($order!=null) {
				$row = array_merge($row,array('order'=>$order));
				$row = array_merge($row,array('Scale_id'=>$scale));
				$row = array_merge($row,array('IdServqualDetail'=>$idservqualdetail));
			}
		}
		else $row=$db->fetchAll($select);
		
		return $row;
		
	}
	
     
}