<?php

/**
 * @author Ain
 */

class Finance_Model_DbTable_Typefee extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_additional';
	protected $_primary = "id";
	
	public function getProgram($id=0){
		$id = (int)$id;
		echo $id."**";
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Promotion Record");
		}
		return $row->toArray();
	}
	
	public function getPaginatePromotion(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from($this->_name);
//             		    ->join('fr_targetgroup_main', 'id = fr_targetgroupid');
//             		    ->join('branch', ' branchID = VENUE');
//						echo $selectData;
		return $selectData;
	}
	

	 public function getPaginateData($cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($this->_name)
		->join('fr_targetgroup_main', 'id = fr_targetgroupid')
		->where('fr_id  ='.$cond);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
		
	public function getListData($table,$cond,$orderby){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->where($cond)
		->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	
	public function addAdditional($postData){
		$data = array(
		        'additional_name' => $postData['txtitem'],
		        'additional_desc' => $postData['txtdesc'],
		        'additional_amount' => $postData['txtamt']
				);
			
		$this->insert($data);
		$id = $this->getAdapter()->lastInsertId();
		return $id;
	}
	
	public function addComp($postData){
		$data = array(
		        'fr_prom_id' => $postData['txtpromid'],
		        'fr_charge_period' => $postData['txtcharge'],
		        'fr_semid' => $postData['txtsem'],
		        'fr_comp_group' => $postData['txtbgroup'],
		        'fr_comp_desc' => $postData['txtbtype'],
		        'fr_comp_fee' => $postData['txtfee']
				);
			
		$this->insert($data);
		$id=$postData['txtpromid'];
		//$id = $this->getAdapter()->lastInsertId();
		return $id;
	}
	
	public function updateProgram($postData,$id){
		$data = array(
		        'group_code' => $postData['txtcode'],
				'group_desc' => $postData['txtdesc']
				);
			
		$this->update($data, 'id = '. (int)$id);
	}
	
	public function deleteProgram($id){
		$this->delete('ID =' . (int)$id);
	}

}

