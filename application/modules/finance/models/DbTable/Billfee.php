<?php

/**
 * @author Ain
 */

class Finance_Model_DbTable_Billfee extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_promotion';
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

	public function getFeeData($table,$tbljoin,$joincond,$tbljoin2,$joincond2,$tbljoin3,$joincond3,$tbljoin4,$joincond4,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		echo $select = $db->select()
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
	
	public function addFee($postData){
		$data = array(
		        'fr_targetgroupid' => $postData['txtpromote'],
		        'fr_targetcode' => $postData['txtprcode'],
		        'fr_total_sem' => $postData['txtsem'],
		        'fr_month' => $postData['txtmth']
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
	
	public function addPlan($table,$postData){
		
		$data = array(
		        'id_fee' => $postData['id_fee'],
		        'id_plan' => $postData['id_plan'],
		        'item' => $postData['item'],
		        'add_charge' => $postData['add_charge'],
		        'amount' => $postData['amount']
				);
				
		$stmt = $this->getAdapter()->insert($table,$data);
		$id = $this->getAdapter()->lastInsertId();
		return $stmt;
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
	
	public function deleteData($table,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
//		echo $sql = "DELETE FROM fr_promotion_component WHERE id_comp = ".$id;
//		//$stmt = $sql->query();
//		$db->query($sql);
		
//		$select = $db->select()
//		->from($table)
//		->delete('id =' . (int)$id);
//		
		//$select = $db->query('DELETE FROM '.$table.' WHERE id = 1'); 
		
		$n = $db->delete($table, 'id_comp = '.$id);
		
     // $table = new Bugs();
//      $where = $db->quoteInto($table.' = ?', $id);
//      $db->quoteInto('WHERE foo IN ?', $db->select()->from('subqueryTable'));
//      $db->delete($where);
		
//		echo $select = $db->select()
//		->delete(' id = ' . (int)$id)
//		->from($table);
	}
	
	


}

