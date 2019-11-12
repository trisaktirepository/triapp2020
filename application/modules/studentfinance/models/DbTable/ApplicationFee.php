<?php
class Studentfinance_Model_DbTable_ApplicationFee extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'application_fee';
	protected $_primary = "idfee";
		
	public function getData($intake,$program,$testcode){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fi'=>$this->_name))
					->where('fi.idintake=?',$intake)
					->where('fi.idprogram=?',$program)
					->where('fi.placement_code=?',$testcode);
		$row=$db->fetchRow($selectData);
		 if (!$row) {
		 	$selectData = $db->select()
		 	->from(array('fi'=>$this->_name))
		 	->where('fi.idintake=?',$intake) 
		 	->where('fi.placement_code=?',$testcode);
		 	$row=$db->fetchRow($selectData);
		 }	
		return $row;
	}
	
	public function getPaginateData($search=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($search){
			$selectData = $db->select()
					->from(array('fi'=>$this->_name))
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
					->from(array('fi'=>$this->_name))
					->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = fi.fi_amount_calculation_type', array('calType'=>'d.DefinitionDesc','calTypeBahasa'=>'d.Description'))
					->joinLeft(array('e'=>'tbl_definationms'),'e.idDefinition = fi.fi_frequency_mode',array('freqMode'=>'e.DefinitionDesc','freqModeBahasa'=>'e.Description'))
					->where("fi.fi_active = 1");
						
		}
			
		return $selectData;
	}
	
		
	public function addData($postData){
		
		$data = array(
		        'fi_name' => $postData['fi_name'],
				'fi_name_bahasa' => $postData['fi_name_bahasa'],
				'fi_name_short' => $postData['fi_name_short'],
				'fi_code' => $postData['fi_code'],
				'fi_amount_calculation_type' => $postData['fi_amount_calculation_type'],
				'fi_frequency_mode' => $postData['fi_frequency_mode']				
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

