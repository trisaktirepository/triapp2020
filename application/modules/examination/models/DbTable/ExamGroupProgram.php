<?php 

class Examination_Model_DbTable_ExamGroupProgram extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group_program';
	protected $_primary = "egp_id";
	
	public function getGroupData($exam_group_id){
	
	  $db = Zend_Db_Table::getDefaultAdapter();
	
	  $select = $db ->select()
	  ->from(array('egp'=>$this->_name))
	  ->join(array('p'=>'tbl_program'), 'p.IdProgram = egp.egp_program_id')
	  ->where('egp.egp_eg_id = ?',$exam_group_id);
	  	
	  $row = $db->fetchAll($select);
	  	
	  return $row;
	}
	
	public function add($data=array()){
	 	
		return parent::insert($data);
	}
	
	public function updateDataProgram($data=array(),$groupid,$programid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->update($this->_name,$data,'egp_eg_id='.$groupid.' and egp_program_id='.(int)$programid);
	}
}