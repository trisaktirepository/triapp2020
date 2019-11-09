<?php
//require_once 'Zend/Controller/Action.php';
class GeneralSetup_Model_DbTable_Language extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'sis_language';
	protected $_primary = "sl_id";
		
	public function getLanguage($id=0){
		//echo "x";
	}	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sl'=>$this->_name))
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
		        'sl_var_name' => $postData['sl_var_name'],
				'sl_english' => $postData['sl_english'],
				'sl_bahasa' => $postData['sl_bahasa']				
				);
			
		$this->insert($data);
	}		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sl'=>$this->_name))
					->where("sl_id=$id");
		//echo $selectData;
		$row = $db->fetchRow($selectData);				
		return $row;
	}	

	public function updateData($postData,$id){
		
		$data = array(
		        'sl_var_name' => $postData['sl_var_name'],
				'sl_english' => $postData['sl_english'],
				'sl_bahasa' => $postData['sl_bahasa']
				);
			
		$this->update($data, 'sl_id = '. (int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('sl_id = '. (int)$id);
		}
	}	
}

