<?php
class Studentfinance_Model_DbTable_FeeStructureItem extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_item';
	protected $_primary = "fsi_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsi'=>$this->_name))
					->joinLeft(array('fi'=>'fee_item'),'fi.fi_id = fsi.fsi_item_id')
					->where("fsi.fsi_id = '".$id."'");

		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getStructureData($fee_structure_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsi'=>$this->_name))
					->joinLeft(array('fi'=>'fee_item'),'fi.fi_id = fsi.fsi_item_id')
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fi.fi_amount_calculation_type', array('calType'=>'d.DefinitionDesc','calTypeBahasa'=>'d.Description'))
					->joinLeft(array('e'=>'tbl_definationms'),'e.idDefinition = fi.fi_frequency_mode',array('freqMode'=>'e.DefinitionDesc','freqModeBahasa'=>'e.Description'))
					->where("fsi.fsi_structure_id = '".$fee_structure_id."'");
		
		$row = $db->fetchAll($selectData);
		//echo $selectData;
		if($row){
			//insert semester item data
			$feeStructureItemSemesterDb = new Studentfinance_Model_DbTable_FeeStructureItemSemester();
			$i=0;
			foreach ($row as $data){
				$row[$i]['semester'] = $feeStructureItemSemesterDb->getStructureItemData($data['fsi_id']);
				$i++;
			}
			
			//insert subject item data
			$feeStructureItemSubjectDb = new Studentfinance_Model_DbTable_FeeStructureItemSubject();
			$i=0;
			foreach ($row as $data){
				$row[$i]['subject'] = $feeStructureItemSubjectDb->getStructureItemData($data['fsi_id']);
				$i++;
			}
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
		        'fsi_structure_id' => $postData['fsi_structure_id'],
				'fsi_item_id' => $postData['fsi_item_id'],
				'fsi_amount' => $postData['fsi_amount']				
		);
			
		$this->insert($data);
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
		        'fsi_structure_id' => $postData['fsi_structure_id'],
				'fsi_item_id' => $postData['fsi_item_id'],
				'fsi_amount' => $postData['fsi_amount']				
		);
			
		$this->update($data, "fsi_id = '".$id."'");
	}
	
	public function updateAmount($postData,$id){
		
		$data = array(
				'fsi_amount' => $postData['fsi_amount']				
		);
			
		$this->update($data, "fsi_id = '".$id."'");
	}
	
	public function deleteData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$db->beginTransaction();
		try {
			//delete all item semester
			$result = $db->delete('fee_structure_item_semester', 'fsis_item_id = '.$id);
			
			
			//delete item
			$result = $db->delete('fee_structure_item', 'fsi_id = '.$id);
			
	
			$this->_db->commit();
			
		} catch (Exception $e) {
     		// rollback
     		$this->_db->rollback();
     		echo $e->getMessage();
     		
     		exit;
		}
	}	
}

