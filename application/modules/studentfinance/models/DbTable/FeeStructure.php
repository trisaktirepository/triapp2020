<?php
class Studentfinance_Model_DbTable_FeeStructure extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure';
	protected $_primary = "fs_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
					->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
					->where("fs.fs_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
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
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		//add fs data
	   	$data = array(
	        'fs_name' => $postData['fs_name'],
			'fs_description' => $postData['fs_description'],
			'fs_intake_start' => $postData['fs_intake_start'],
			//'fs_intake_end' => $postData['fs_intake_end'],
			'fs_student_category' => $postData['fs_student_category']				
		);
		
		$this->insert($data);
			
		/*
		$db->beginTransaction();
		try{
			//set end intake for fee structure with same student category
			$data = array(
						'fs_intake_end' => $postData['fs_intake_start']
					);

			$where = array(
						'fs_student_category = ?' => $postData['fs_student_category'],
						'fs_intake_end is null'
					);

			$this->update($data,$where);		
			
			//add fs data
		   	$data = array(
		        'fs_name' => $postData['fs_name'],
				'fs_description' => $postData['fs_description'],
				'fs_intake_start' => $postData['fs_intake_start'],
				//'fs_intake_end' => $postData['fs_intake_end'],
				'fs_student_category' => $postData['fs_student_category']				
			);
			
			$this->insert($data);
		   
		   	$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			echo $e->getMessage();
		}*/
		
		
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
		        'fs_name' => $postData['fs_name'],
				'fs_description' => $postData['fs_description'],
				'fs_intake_start' => $postData['fs_intake_start'],
				'fs_intake_end' => $postData['fs_intake_end']!=null?$postData['fs_intake_end']:null,
				'fs_student_category' => $postData['fs_student_category']				
			);
			
		$this->update($data, "fs_id = '".$id."'");
	}
	
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'fi_active' => 0				
			);
				
			$this->update($data, "fi_id = '".$id."'");
		}
	}	
	
	public function getApplicantFeeStructure($intake_id, $program_id, $student_category=314,$branch=null,$majoring=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->join( array('fsp'=>'fee_structure_program'), 'fsp.fsp_fs_id = fs.fs_id and fsp.fsp_program_id = '.$program_id)
					->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
					//->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
					->where("fs.fs_student_category = '".$student_category."'")
					->where("fs.fs_intake_start = '".$intake_id."'");
		if ($branch!=null) $selectData->where('fsp.fsp_branch_id=?',$branch);
		if ($majoring!=null) $selectData->where('fsp.fsp_majoring_id=?',$majoring);
		echo $selectData;exit;
		$row = $db->fetchRow($selectData);
		
		if(!$row){
			$selectData = $db->select()
				->from(array('fs'=>$this->_name))
				->join( array('fsp'=>'fee_structure_program'), 'fsp.fsp_fs_id = fs.fs_id and fsp.fsp_program_id = '.$program_id)
				->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
				//->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
				->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
				->where("fs.fs_student_category = '".$student_category."'")
				->where("fs.fs_intake_start = '".$intake_id."'")
				->where('fsp.fsp_branch_id is null or fsp.fsp_branch_id=0')
				->order('i.ApplicationStartDate DESC');
			if ($majoring!=null) $selectData->where('fsp.fsp_majoring_id=?',$majoring);
			$row = $db->fetchRow($selectData);
			if(!$row){
				$selectData = $db->select()
				->from(array('fs'=>$this->_name))
				->join( array('fsp'=>'fee_structure_program'), 'fsp.fsp_fs_id = fs.fs_id and fsp.fsp_program_id = '.$program_id)
				->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
				//->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
				->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
				->where("fs.fs_student_category = '".$student_category."'")
				->where("fs.fs_intake_start = '".$intake_id."'")
				->where('fsp.fsp_majoring_id is null or fsp.fsp_majoring_id=0')
				->order('i.ApplicationStartDate DESC');
				if ($branch!=null) $selectData->where('fsp.fsp_branch_id=?',$branch); 
				$row = $db->fetchRow($selectData);
				if (!$row) {
					$selectData = $db->select()
					->from(array('fs'=>$this->_name))
					->join( array('fsp'=>'fee_structure_program'), 'fsp.fsp_fs_id = fs.fs_id and fsp.fsp_program_id = '.$program_id)
					->joinLeft(array('i'=>'tbl_intake'),'i.IdIntake = fs.fs_intake_start', array('s_IntakeId'=>'','s_Intake'=>'IntakeDesc', 's_intake_bahasa'=>'IntakeDefaultLanguage', 'start_date'=>'ApplicationStartDate', 'end_date'=>'ApplicationEndDate'))
					//->joinLeft(array('ii'=>'tbl_intake'),'ii.IdIntake = fs.fs_intake_end', array('s_IntakeId'=>'','e_Intake'=>'IntakeDesc', 'e_intake_bahasa'=>'IntakeDefaultLanguage', 'e_start_date'=>'ApplicationStartDate', 'e_end_date'=>'ApplicationEndDate' ))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fs.fs_student_category')
					->where("fs.fs_student_category = '".$student_category."'")
					->where("fs.fs_intake_start = '".$intake_id."'")
					->where('fsp.fsp_branch_id is null or fsp.fsp_branch_id=0')
					->where('fsp.fsp_majoring_id is null or fsp.fsp_majoring_id=0')
					->order('i.ApplicationStartDate DESC');
					//if ($branch!=null) $selectData->where('fsp.fsp_branch_id=?',$branch);
					$row = $db->fetchRow($selectData);
				}
			}
			
		}
		
		//echo var_dump($row);exit;
		//if(!$row){
			//throw new Exception("No Fee Structure setup for this program or student category");
		//}else{
			return $row;
		//}
	}
}

