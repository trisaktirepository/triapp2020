<?php

/**
 * @author Ain
 */

class Finance_Model_DbTable_Fee extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_fee';
	protected $_primary = "fr_id";
	
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
	

	public function getPaginateFee(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from($this->_name)
             		     ->join('masterprogram', 'masterProgramID = fr_progid')
             		    ->join('fr_targetgroup_main', 'id = fr_promoteid');
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
	
	public function getData2($table,$tablejoin,$conjoin,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
//		$table = $this->_name;
		$select = $db->select()
		->from($table)
		->join($tablejoin, $conjoin)
		->where($cond);
//		->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function addAction()
    {
	   	//title
    	$this->view->title="Add  Fee";
    }
    

	public function assignFeeProgramID($feeID,$programID,$addid)
    {
        $data = array(
            'fr_promoteid' => $feeID,
            'fr_progid' => $programID,
            'fr_additionid' => $addid
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

