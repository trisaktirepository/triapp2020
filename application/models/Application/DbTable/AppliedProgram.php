<?php

class App_Model_Application_DbTable_AppliedProgram extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a013_applied';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Applied Program");
		}

		return $row->toArray();
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->order('id');
		
		return $select;
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
	
	public function getAppliedProgram($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('p'=>'r006_program'),'p.id = a.programid',array('program_code'=>'code'))
		->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'))
		->where('applicantid = '.$id);	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function getAppliedProgramdetails($id,$programid){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('p'=>'r006_program'),'p.id = a.programid',array('program_code'=>'code'))
		->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'))
		->where('a.applicantid = '.$id.' and a.programid = '.$programid);	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	
	public function add($data){
//		$data = array(
//		        'programid' => $postData['sc001_name'],
//				'rank' => 0
//				);
				
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
			
	}
	
	public function updateData($postData,$id){
		$data = array(
				'sc001_name' => $postData['sc001_name']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

