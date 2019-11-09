<?php
//require_once 'Zend/Controller/Action.php';
class GeneralSetup_Model_DbTable_SchoolDisciplineProgramme extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'appl_program_req';
	protected $_primary = "apr_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('apr'=>$this->_name))
					->where("apr.apr_id = ?", $id);
		//echo $selectData;

		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getDisciplineData($discipline_code,$academicYear){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
					->from(array('apr'=>$this->_name))
					->joinLeft(array('tp'=>'tbl_program'),'tp.ProgramCode = apr.apr_program_code')
					->where("apr.apr_decipline_code = '".$discipline_code."'")
					->where("apr.apr_academic_year = '".$academicYear."'");
			
		//echo $sql;
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
		        'apr_academic_year' => $postData['apr_academic_year'],
				'apr_program_code' => $postData['apr_program_code'],
				'apr_decipline_code' => $postData['apr_decipline_code']
				);
			
		$this->insert($data);
	}		
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('apr_id = '. (int)$id);
		}
	}	
}

