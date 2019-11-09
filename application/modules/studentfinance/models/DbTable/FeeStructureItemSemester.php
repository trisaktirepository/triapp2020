<?php
class Studentfinance_Model_DbTable_FeeStructureItemSemester extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_structure_item_semester';
	protected $_primary = "fsis_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsis'=>$this->_name))
					->where("fsis.fsis_id = '".$id."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getStructureItemData($fee_structure_item_id, $sem=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fsis'=>$this->_name))
					->where("fsis.fsis_item_id = '".$fee_structure_item_id."'");
					
		if($sem!=null){
			$selectData->where('fsis.fsis_semester =?', $sem);
			
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
		
	public function addData($postData){
		
		$data = array(
		        'fsis_item_id' => $postData['fsis_item_id'],
				'fsis_semester' => $postData['fsis_semester']				
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

	public function updateItemData($postData){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$db->beginTransaction();
		try {
			//delete all item semester
			$result = $db->delete('fee_structure_item_semester', 'fsis_item_id = '.$postData['fsis_item_id']);
			
			
			//add new item semester
			foreach ($postData['fsis_semester'] as $sem) {
				$data = array( 
					'fsis_item_id' => $postData['fsis_item_id'],
					'fsis_semester' => $sem
				);
				
				$db->insert('fee_structure_item_semester', $data);
			}
			
	
			$this->_db->commit();
			
		} catch (Exception $e) {
     		// rollback
     		$this->_db->rollback();
     		echo $e->getMessage();
     		
     		exit;
		}
	}
}

