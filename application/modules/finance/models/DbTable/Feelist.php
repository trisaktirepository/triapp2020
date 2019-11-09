<?php

/**
 * @author Ain
 */

class Finance_Model_DbTable_Feelist extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_promotion';
	protected $_name2 = 'fr_fee';
	protected $_primary = "fr_id";
	
	public function getProgram($id=0){
		$id = (int)$id;
		
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
						->from($this->_name)
             		    ->join('fr_targetgroup_main', 'id = fr_targetgroupid');
//             		    ->join('branch', ' branchID = VENUE');
						echo $selectData;
		return $selectData;
	}
	
	public function getPaginateFee($cond){
//		$cond=1;
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from($this->_name2)
             		    ->join('masterprogram', 'masterProgramID = fr_progid')
             		    ->join('fr_targetgroup_main', 'id = fr_promoteid')
             		    ->join('fr_promotion', 'fr_targetgroupid = fr_promoteid')
             		    ->where('fr_promotion.fr_status ='.$cond);
             		    
//						echo $selectData;
//						
//						
//						$stmt = $selectData->query();
//		$result = $stmt->fetchAll();
//		
//		return $result;
		
		return $selectData;
	}

	public function getFeeData($table,$tbljoin,$joincond,$tbljoin2,$joincond2,$tbljoin3,$joincond3,$tbljoin4,$joincond4,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->join($tbljoin2, $joincond2)
		->join($tbljoin3, $joincond3)
		->joinLeft($tbljoin4, $joincond4)
		->where('fr_prom_id  ='.$cond)	
		->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function getCount($table){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
             ->from(array($table),
                    array('total' => 'COUNT(*)'));
            // ->group('p.product_id');
            
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
	
	
	


}

