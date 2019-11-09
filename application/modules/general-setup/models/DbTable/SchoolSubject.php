<?php
//require_once 'Zend/Controller/Action.php';
class GeneralSetup_Model_DbTable_SchoolSubject extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'school_subject';
	protected $_primary = "ss_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('ss'=>$this->_name))
					->where("ss.ss_id = ?", (int)$id);
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getPaginateData($search=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($search){
			$selectData = $db->select()
						->from(array('ss'=>$this->_name))
						->where("ss.ss_subject LIKE '%".$search['ss_subject']."%'")
						->where("ss.ss_subject_bahasa LIKE '%".$search['ss_subject_bahasa']."%'")
						->order('ss.ss_subject ASC');

			if($search['ss_core_subject']=='1'){
				$selectData->where("ss.ss_core_subject = 1");
			}
			
		}else{
			$selectData = $db->select()
					->from(array('ss'=>$this->_name));
		}	
				
		return $selectData;
	}
	
		
	public function addData($postData){
		$auth = Zend_Auth::getInstance();
		
		$data = array(
		        'ss_id' => $postData['ss_id'],
				'ss_subject' => $postData['ss_subject'],
				'ss_subject_bahasa' => $postData['ss_subject_bahasa'],
				'ss_core_subject' => $postData['ss_core_subject']
		);
			
		return $this->insert($data);
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
				'ss_subject' => $postData['ss_subject'],
				'ss_subject_bahasa' => $postData['ss_subject_bahasa'],
				'ss_core_subject' => $postData['ss_core_subject']
		);
			
		$this->update($data, "ss_id = ".(int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->delete("ss_id = ".(int)$id);
		}
	}	
}

