<?php 
class App_Model_Finance_DbTable_FeeStructure extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure';
	protected $_primary = "fi_id";
	
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name))
						->where('a.'.$_primary.' = '.$id);
						
	        $row = $db->fetchRow($select);				
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db ->select()
						->from(array('a'=>$this->_name));
						
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			return null;
		}else{
			return $row;	
		}
	}
		
	public function getApplicantFeeStructure($intake_id, $program_id, $student_category=314){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->join( array('fsp'=>'fee_structure_program'), 'fsp.fsp_fs_id = fs.fs_id and fsp.fsp_program_id = '.$program_id)
					->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
					->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
					->where("fs.fs_student_category = '".$student_category."'")
					->where("fs.fs_intake_start = '".$intake_id."'");
			
		$row = $db->fetchRow($selectData);
		
		if(!row){
			$selectData = $db->select()
				->from(array('fs'=>$this->_name))
				->join( array('fsp'=>'fee_structure_program'), 'fsp.fsp_fs_id = fs.fs_id and fsp.fsp_program_id = '.$program_id)
				->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
				->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
				->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
				->where("fs.fs_student_category = '".$student_category."'")
				->order('i.ApplicationStartDate DESC')
				->limit(1,0);
		}
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	
}
?>