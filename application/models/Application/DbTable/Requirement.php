<?php

class App_Model_Application_DbTable_Requirement extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a007_requirement';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Entry Requirement");
		}

		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		echo $selectData = $db ->select()
						->from(array('entry_req'=>$this->_name))
						->join(array('pm'=>"r006_program"),'entry_req.id_program = pm.id')
						->joinLeft(array('e'=>"a004_education_level"), "entry_req.award = m.id",array('award'=>'name'));
             		    //->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function getEntryRequirement($program_ID=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$program_ID = (int)$program_ID;
		
		if($program_ID!=0){

	        $select = $db->select()
	                 ->from(array('req'=>$this->_name))
	                 ->where('req.entry_id = ' .$program_ID)
	                 ->joinLeft(array('e'=>'a004_education_level'), 'req.education_level = e.id', array('education_level_name'=>'name'))
	                 ->joinLeft(array('qi'=>'a016_qualification_item'), 'req.item = qi.id', array('qualification_item_name'=>'name'));
            			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
        
		}else{
			throw new Exception("There is No given Program");
		}
		
		return $row;
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
		        'id_program' => $postData['program_main_id'],
				'market_id' => $postData['market_id'],
				'entry_name' => $postData['entry_name'],
				'status' => $postData['status']
				);
			
		$this->insert($data);
	}
	
	public function addRequirement($postData){
		$data = array(
		        'entry_id' => $postData['program_id'],
		        'desc' => $postData['desc'],
		        'education_level' => $postData['education_level'],
		        'item' => $postData['item'],
				'condition' => $postData['condition'],
				'compulsory' => $postData['compulsory'],
				'value' => $postData['value']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
		        'entry_id' => $postData['program_id'],
		        'desc' => $postData['desc'],
		        'education_level' => $postData['education_level'],
		        'item' => $postData['item'],
				'condition' => $postData['condition'],
				'compulsory' => $postData['compulsory'],
				'value' => $postData['value']
				);
			
		$this->update($data, 'id = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete('id =' . (int)$id);
	}

}

