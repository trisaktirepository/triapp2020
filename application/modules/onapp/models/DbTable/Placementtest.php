<?php

/**
 * @author Suliana
 */

class Onapp_Model_DbTable_Placementtest extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'placement_test';
	protected $_primary = "ID";
	
	public function getProgram($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Placement Test");
		}

		return $row->toArray();
	}
	
	public function getPaginatePlacementtest(){
		$db = Zend_Db_Table::getDefaultAdapter();
		echo $selectData = $db ->select()
						->from($this->_name)
             		    ->join('program', 'program_id = ID_PROG')
             		    ->join('masterprogram', 'masterProgramID = program_master_id')
             		    ->join('branch', ' branchID = VENUE')
             		    ->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function getData($table,$cond=0){
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
	
	public function addPlacementtest($postData){
		$data = array(
		        'ID_PROG' => $postData['ID_PROG'],
				'DATE' => $postData['datepicker'],
				'VENUE' => $postData['VENUE']
				);
			
		$this->insert($data);
	}
	
	public function updateProgram($postData,$id){
		$data = array(
		        'DATE' => $postData['DATE'],
				'VENUE' => $postData['VENUE']
				);
			
		$this->update($data, 'ID = '. (int)$id);
	}
	
	public function deleteProgram($id){
		$this->delete('ID =' . (int)$id);
	}

}

