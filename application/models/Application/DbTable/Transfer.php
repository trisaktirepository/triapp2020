<?php

class App_Model_Application_DbTable_Transfer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a008_course_transfer';
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
						->join(array('p'=>"r005_program_main"),'pm.program_main_id = p.id',array('main_name'=>'name'))
						->join(array('m'=>"r004_market"), "entry_req.market_id = m.id",array('market_name'=>'name'));
             		    //->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function getcourseTransfer($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
						->from(array('a'=>$this->_name))
						->join(array('b'=>"r010_course"),'a.id_course = b.id')
						->where("a.ard_id = ".$id);
             		    //->order($this->_primary.' DESC');
						
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
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
	
//	public function addData($postData){
//		$data = array(
//		        'ard_id' => $postData['ard_id'],
//				'id_prog' => $postData['id_prog'],
//				'id_course' => $postData['id_course']
//				);
//			
//		$this->insert($data);
//	}
	
	public function addData($data){			
		$id = $this->insert($data);
		$sta = 1;
		return $sta;
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
	
	public function updateProgram($postData,$id){
		$data = array(
		        'DATE' => $postData['DATE'],
				'VENUE' => $postData['VENUE']
				);
			
		$this->update($data, 'ID = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete('ID =' . (int)$id);
	}

}

