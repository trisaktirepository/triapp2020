<?php
class Studentfinance_Model_DbTable_FeeStructurePlan extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_plan';
	protected $_primary = "fsp_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->where("fsp.fsp_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getStructureData($fee_structure_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->where("fsp.fsp_structure_id = '".$fee_structure_id."'");
			
		$row = $db->fetchAll($selectData);	

		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getBillingPlan($fee_structure_id,$billing=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsp'=>$this->_name))
					->where("fsp.fsp_structure_id = '".$fee_structure_id."'")
					->where('fsp.fsp_billing_no = '.substr($billing,0,1) );

		$row = $db->fetchRow($selectData);	

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
		        'fsp_structure_id' => $postData['fsp_structure_id'],
				'fsp_name' => $postData['fsp_name'],
				'fsp_billing_no' => $postData['fsp_billing_no'],
				'fsp_bil_installment' => $postData['fsp_bil_installment']				
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
			$data = array(
				'fi_active' => 0				
			);
				
			$this->update($data, "fi_id = '".$id."'");
		}
	}	
}

