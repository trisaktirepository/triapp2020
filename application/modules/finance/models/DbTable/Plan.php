<?php

/**
 * @author Suliana
 */

class Finance_Model_DbTable_Plan extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_plan_new';
	protected $_primary = "id";
	
	public function getProgram($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		return $row->toArray();
	}
	
	public function getPaginateData2(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from($this->_name);
//             		    ->join('masterprogram', 'masterProgramID = fr_progid')
//             		    ->join('fr_targetgroup_main', 'id = fr_promoteid');
//             		    ->join('branch', ' branchID = VENUE');
						echo $selectData;
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
		
	public function getListData($table,$cond=0,$orderby){
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
	
	public function getData($tablejoin,$conjoin,$tablejoin2,$conjoin2,$cond=0,$orderby){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$table = $this->_name;
		$select = $db->select()
		->from($table)
		->join($tablejoin, $conjoin)
		->join($tablejoin2, $conjoin2)
		->where($cond)
		->order($orderby);
		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	
	public function addAction()
    {
	   	//title
    	$this->view->title="Add  Fee";
    }
    
    public function addPlan($postData){
		$data = array(
		        'id_fee' => $postData['id_fee'],
		        'id_plan' => $postData['id_fee'],
		        'item' => $postData['id_fee'],
		        'add_charge' => $postData['id_fee'],
		        'amount' => $postData['id_fee']
				);
			
		$this->insert($data);
		$id = $this->getAdapter()->lastInsertId();
		return $id;
	}
	
    

	public function assignFeeProgramID($feeID,$programID)
    {
        $data = array(
            'fr_promoteid' => $feeID,
            'fr_progid' => $programID,
            'fr_additionid' => 0
        );
        $this->insert($data);
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

