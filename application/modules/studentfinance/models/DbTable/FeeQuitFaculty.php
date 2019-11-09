<?php
class Studentfinance_Model_DbTable_FeeQuitFaculty extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_quit_faculty';
	protected $_primary = "id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fq'=>$this->_name));
		
		if($id!=0){
			$selectData->where("fq.id = '".$id."'");
			
			$row = $db->fetchRow($selectData);
		}else{
			
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}				
		
	}
	
	public function getDataCollege($head_id, $college_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fqf'=>$this->_name))
					->where("fqf.fq_id = ?", $head_id)
					->where("fqf.college_id = ?", $college_id);
			
		$row = $db->fetchRow($selectData);
		
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}				
		
	}
	
	public function getQuitCharges($faculty_id, $intake_id=78, $txn_date=null){
		
		if($txn_date==null){
			$txn_date = date('Y-m-d');
			//$txn_date = date('Y-m-d',strtotime('2013-08-24'));
		}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
					->from(array('fq'=>'fee_quit'))
					->where("fq.intake_id = ?", $intake_id)
					->where("fq.last_effective_date >= ?", $txn_date);
		
		$row = $db->fetchRow($select);
		
		//get last efective date for intake	
		if(!$row){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
						->from(array('fq'=>'fee_quit'))
						->where("fq.intake_id = ?", $intake_id)
						->order('fq.last_effective_date desc');
						
			$row = $db->fetchRow($select);
		}
		
		$charges = $this->getDataCollege($row['id'], $faculty_id);
		
		if(!$charges){
			return 0;
		}else{
			return $charges['amount'];	
		}
		
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
	
		
		
		
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'status' => 0				
			);
				
			$this->update($data, "id = '".$id."'");
		}
	}	
}

