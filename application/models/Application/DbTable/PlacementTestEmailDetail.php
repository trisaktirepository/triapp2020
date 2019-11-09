<?php

class App_Model_Application_DbTable_PlacementTestEmailDetail extends Zend_Db_Table_Abstract {

	protected $_name = 'email_template_detl';
	protected $_primary = "etd_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('etd'=>$this->_name))
					->where('etd.etd_id = '.$id);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('etd'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
			
	public function addData($postData){
		
		$data = array(
		        'etd_eth_id' => $postData['etd_eth_id'],
				'etd_language' => $postData['etd_language'],
				'etd_subject' => $postData['etd_subject'],
				'etd_body' => $postData['etd_body']
		);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'etd_eth_id' => $postData['etd_eth_id'],
				'etd_language' => $postData['etd_language'],
				'etd_subject' => $postData['etd_subject'],
				'etd_body' => $postData['etd_body']
		);
			
		$this->update($data, 'etd_id = '. (int)$id);
	}
	
}

