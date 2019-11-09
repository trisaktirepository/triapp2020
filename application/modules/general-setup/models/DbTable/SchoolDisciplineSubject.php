<?php
//require_once 'Zend/Controller/Action.php';
class GeneralSetup_Model_DbTable_SchoolDisciplineSubject extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'school_decipline_subject';
	protected $_primary = "sds_id";
		
	public function getDisciplineData($discipline_code){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
					->from(array('sds'=>$this->_name))
					->joinLeft(array('ss'=>'school_subject'),'ss.ss_id = sds.sds_subject')
					->where("sds.sds_discipline_code = '".$discipline_code."'");
			
		$row = $db->fetchAll($sql);
				
		if($row){
			return $row;	
		}else{
			return false;
		}
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sds'=>$this->_name))
					->order('sl_var_name ASC');	
		return $selectData;
	}
	
	public function searchPaginate($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sl'=>$this->_name))
					->where("sl_var_name LIKE '%".$post['sl_var_name']."%' OR 
					  sl_english LIKE '%".$post['sl_var_name']."%' OR  
					  sl_bahasa LIKE '%".$post['sl_var_name']."%'")
					->order('sl_var_name ASC')
					;
								
		return $selectData;
	}
	
	public function addData($postData){
		
		$data = array(
		        'sds_discipline_code' => $postData['sds_discipline_code'],
				'sds_subject' => $postData['sds_subject']			
				);
			
		$this->insert($data);
	}		
	
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sds'=>$this->_name))
					->where("sds.sds_id = ?", $id);
		//echo $selectData;

		$row = $db->fetchRow($selectData);				
		return $row;
	}	

	/*public function updateData($postData,$id){
		
		$data = array(
		        'sl_var_name' => $postData['sl_var_name'],
				'sl_english' => $postData['sl_english'],
				'sl_bahasa' => $postData['sl_bahasa']
				);
			
		$this->update($data, 'sl_id = '. (int)$id);
	}*/
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('sds_id = '. (int)$id);
		}
	}	
}

