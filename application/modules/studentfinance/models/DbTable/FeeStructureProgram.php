<?php
class Studentfinance_Model_DbTable_FeeStructureProgram extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_program';
	protected $_primary = "fsp_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.IdProgram = fsp.fsp_program_id')
					->where("fsp.fsp_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getStructureData($fee_structure_id, $program_id = 0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.IdProgram = fsp.fsp_program_id')
					->where("fsp.fsp_fs_id = '".$fee_structure_id."'");
					
		if($program_id!=0){
			$selectData->where('fsp.fsp_program_id = '.$program_id);
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
	
	public function getPaginateData($search=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($search){
			$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fi.fi_amount_calculation_type', array('calType'=>'d.DefinitionDesc','calTypeBahasa'=>'d.Description'))
					->joinLeft(array('e'=>'tbl_definationms'),'e.idDefinition = fi.fi_frequency_mode',array('freqMode'=>'e.DefinitionDesc','freqModeBahasa'=>'e.Description'))
					->where("fi.fi_name LIKE '%".$search['fi_name']."%'")
					->where("fi.fi_name_bahasa LIKE '%".$search['fi_name_bahasa']."%'")
					->where("fi.fi_name_short LIKE '%".$search['fi_name_short']."%'")
					->where("fi.fi_code LIKE '%".$search['fi_code']."%'")
					->where("fi.fi_amount_calculation_type LIKE '%".$search['fi_amount_calculation_type']."%'")
					->where("fi.fi_frequency_mode LIKE '%".$search['fi_frequency_mode']."%'")
					->where("fi.fi_active = 1");	
		}else{
			$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
					->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage'))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category');
						
		}
			
		return $selectData;
	}
	
		
	public function addData($postData){
		
		$data = array(
		        'fsp_fs_id' => $postData['fsp_fs_id'],
				'fsp_program_id' => $postData['fsp_program_id'],
				'fsp_first_sem_sks' => $postData['fsp_first_sem_sks']				
		);
			
		$this->insert($data);
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
		        'fi_name' => $postData['fi_name'],
				'fi_name_bahasa' => $postData['fi_name_bahasa'],
				'fi_name_short' => $postData['fi_name_short'],
				'fi_code' => $postData['fi_code'],
				'fi_amount_calculation_type' => $postData['fi_amount_calculation_type'],
				'fi_frequency_mode' => $postData['fi_frequency_mode']				
		);
			
		$this->update($data, "fi_id = '".$id."'");
	}
	
	public function deleteData($id=null){
		if($id!=null){
			$this->delete("fsp_id = '".$id."'");
		}
	}	
}

