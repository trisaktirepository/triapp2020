<?php

class App_Model_Application_DbTable_EntryRequirement extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a005_entry_requirement';
	protected $_primary = "id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from($this->_name)
	                 ->where($this->_primary.' = ' .$id);
			                     
	        $stmt = $db->query($select);
	        $row = $db->fetchRow($select);
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('entry_req'=>$this->_name),array('entry_id'=>'id','entry_name'=>'entry_name','status_entry'=>'status'))
						->joinLeft(array('pm'=>"r006_program"),'entry_req.id_program = pm.id');
             		    //->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function getSelectData($table,$cond=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->where($cond);
		//->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getList($table,$tbljoin,$joincond,$tbljoin2=0,$joincond2=0,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->join($tbljoin2, $joincond2)
		//->join($tbljoin3, $joincond3)
		->where($cond);	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function addData($postData){
		$data = array(
		        'id_program' => $postData['id_program'],
				'market_id' => $postData['market_id'],
				'entry_name' => $postData['entry_name'],
				'status' => $postData['status']
				);
			
		$this->insert($data);
	}
	
	public function updateAdditionalData($postData,$id){
		
		$data= array(
					'ARD_PROGRAM_NAME'=>$postData['id_apply'],
					'ARD_SEX'=>$postData['ARD_SEX'],
					'ARD_DOB'=>$postData['ARD_YEAR'].'-'.$postData['ARD_MONTH'].'-'.$postData['ARD_DAY'],
					'ARD_RACE'=>$postData['ARD_RACE'],
					'ARD_RELIGION'=>$postData['ARD_RELIGION'],
					'ARD_MARITAL'=>$postData['ARD_MARITAL'],
					'ARD_CITIZEN'=>$postData['ARD_CITIZEN'],
					'ARD_ADDRESS1'=>$postData['ARD_ADDRESS1'],
					'ARD_ADDRESS2'=>$postData['ARD_ADDRESS2'],
					'ARD_POSTCODE'=>$postData['ARD_POSTCODE'],
					'ARD_STATE'=>$postData['ARD_STATE']
					);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function updateData($postData,$id){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
				'entry_name' => $postData['entry_name'],
				'status' => $postData['status'],
				'createddt' => date("Y-m-d H:i:s"),
      	 		'createdby' => $auth->getIdentity()->id,
				);
			
		$this->update($data, 'ID = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete('id =' . (int)$id);
	}

}

