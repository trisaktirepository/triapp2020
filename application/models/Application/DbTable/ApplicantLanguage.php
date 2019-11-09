<?php

class App_Model_Application_DbTable_ApplicantLanguage extends Zend_Db_Table_Abstract {

	protected $_name = 'applicant_language';
	protected $_primary = "al_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('al'=>$this->_name))
					->where('al.al_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('al'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	public function addData($postData){
		
		$data = array(
	        'apd_placement_code' => $postData['apd_placement_code'],
			'apd_comp_code' => $postData['apd_comp_code'],
			'apd_total_question' => $postData['apd_total_question'],
			'apd_questno_start' => $postData['apd_questno_start'],
			'apd_questno_end' => $postData['apd_questno_end']
		);
				
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
	        'apd_placement_code' => $postData['apd_placement_code'],
			'apd_comp_code' => $postData['apd_comp_code'],
			'apd_total_question' => $postData['apd_total_question'],
			'apd_questno_start' => $postData['apd_questno_start'],
			'apd_questno_end' => $postData['apd_questno_end']
		);
		
		$this->update($data, 'apd_id = '. (int)$id);
	}
	
	public function deleteData($data,$id){
		if($id!=0){
			$this->update($data, 'ac_id = '. (int)$id);
		}
	}
}

