<?php
class Studentfinance_Model_DbTable_FeeStructureDiscountRank extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_discount_rank';
	protected $_primary = "fdr_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fspd'=>$this->_name))
					->where("fspd.fspd_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getPlanData($fee_structure_id, $rank){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsi'=>'fee_structure_item'))
					->joinLeft(array('fdr'=>'fee_discount_rank'), 'fdr_item_id = fsi.fsi_id and fdr.fdr_rank = '.$rank )
					->joinLeft(array('fi'=>'fee_item'),'fi.fi_id = fsi.fsi_item_id')
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fi.fi_amount_calculation_type', array('calType'=>'d.DefinitionDesc','calTypeBahasa'=>'d.Description'))
					->joinLeft(array('e'=>'tbl_definationms'),'e.idDefinition = fi.fi_frequency_mode',array('freqMode'=>'e.DefinitionDesc','freqModeBahasa'=>'e.Description'))
					->where("fsi.fsi_structure_id = '".$fee_structure_id."'");
			
		$row = $db->fetchAll($selectData);	

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
			'fdr_rank' => $postData['fdr_rank'],
			'fdr_item_id' => $postData['fdr_item_id'],	
			'fdr_percentage' => $postData['fdr_percentage']!="null"?$postData['fdr_percentage']:null,
			'fdr_amount' => $postData['fdr_amount']!="null"?$postData['fdr_amount']:null			
		);
			
		return $this->insert($data);
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
			'fdr_percentage' => $postData['fdr_percentage']!="null"?$postData['fdr_percentage']:null,
			'fdr_amount' => $postData['fdr_amount']!="null"?$postData['fdr_amount']:null			
		);
			
		$this->update($data, "fdr_id = '".$id."'");
	}
	
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'fi_active' => 0				
			);
				
			$this->update($data, "fdr_id = '".$id."'");
		}
	}	
}

