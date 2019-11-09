<?php

class App_Model_Application_DbTable_PlacementTestEmail extends Zend_Db_Table_Abstract {

	protected $_name = 'email_template_head';
	protected $_primary = "eth_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('eth'=>$this->_name))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = eth.eth_create_by', array('eth_create_by_name'=>'fName'))
					->where('eth.eth_id = '.$id);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('al'=>$this->_name))
						->joinLeft(array('u'=>'tbl_user'),'u.iduser = eth.eth_create_by', array('eth_create_by_name'=>'fName'));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getHeadDetail($headId, $LanguageId=0){
		
		if($LanguageId!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('etd'=>'email_template_detl'))
					->joinLeft(array('al'=>'applicant_language'),'al.al_id = etd.etd_language')
					->where('etd.etd_eth_id = '.$headId)
					->where('etd.etd_language =?',$LanguageId);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('etd'=>'email_template_detl'))
					->joinLeft(array('al'=>'applicant_language'),'al.al_id = etd.etd_language')
					->where('etd.etd_eth_id = '.$headId);
								
			$row = $db->fetchAll($select);
		}
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('eth'=>$this->_name))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = eth.eth_create_by', array('eth_create_by_name'=>'fName'))
					->order('eth_template_name 	ASC');
						
		return $selectData;
	}
	
		
	public function addData($postData){
		
		$data = array(
		        'eth_template_name' => $postData['eth_template_name'],
				'eth_email_from' => $postData['eth_email_from'],
				'eth_email_from_name' => $postData['eth_email_from_name'],
				'eth_create_by' => $postData['eth_create_by'],
				'eth_create_date' => $postData['eth_create_date']
		);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'eth_template_name' => $postData['eth_template_name'],
				'eth_email_from' => $postData['eth_email_from'],
				'eth_email_from_name' => $postData['eth_email_from_name']
		);
			
		$this->update($data, 'eth_id = '. (int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('eth_id = '. (int)$id);
		}
	}

}

