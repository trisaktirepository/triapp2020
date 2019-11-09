<?php
class Studentfinance_Model_DbTable_FeeQuit extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fee_quit';
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
	
	public function getDataIntake($intake=0, $status=1){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('fq'=>$this->_name))
					->where("fq.intake_id = ?", $intake)
					->where("fq.status = ?", $status)
					->order("fq.last_effective_date");
			
		$row = $db->fetchAll($selectData);
		
			
		if(!$row){
			return null;
		}else{
			return $row;	
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
	
		
	public function insert(array $data){
		
		$auth = Zend_Auth::getInstance();
		
		$data['update_by'] = $auth->getIdentity()->iduser;
		$data['update_date'] = date('Y-m-d H:i:s');
			
        return parent::insert($data);
	}		
		

	public function updateData(array $data,$where){
		
		$data['update_by'] = $auth->getIdentity()->iduser;
		$data['update_date'] = date('Y-m-d H:i:s');
		
		return parent::update($data, $where);
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

