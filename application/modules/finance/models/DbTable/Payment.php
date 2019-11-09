<?php
/**
 * @author Suliana
 */

class Finance_Model_DbTable_Payment extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_payment_plan';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Such Applicant.");
		}
		return $row->toArray();
	}
	
	public function getList($sel,$table,$cond=0,$orderby){ //distinct
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->distinct()
				//->from($table)
				//->from(array('p' => $table), $sel)
				->from($table,array($sel))
				->where($cond)
				->order($orderby);

		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getList2($table,$cond=0,$orderby){
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
	
	public function getpayment($table,$cond=0,$group){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->where($cond)
		->group($group);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getListData($table,$cond=0,$tbljoin=0,$joincond=0,$orderby){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->where($cond)
		->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getFeeData($sel,$table,$tbljoin,$joincond,$tbljoin2,$joincond2,$tbljoin3,$joincond3,$tbljoin4,$joincond4,$cond,$group){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		echo $select = $db->select()
		->from($table,array($sel))
		->join($tbljoin, $joincond)
		->join($tbljoin2, $joincond2)
		->join($tbljoin3, $joincond3)
		->joinLeft($tbljoin4, $joincond4)
		->where($cond)	
		->group($group)	
		->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function getListData2($table,$cond=0,$tbljoin=0,$joincond=0,$group,$orderby){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->where($cond)
		->group($group)
		->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getPaginateData($table,$order){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from($table)
					//->join('hg015_branch', 'ha001_hg015_branch_id = hg015_branch_id')
					->order($order);
		
		return $select;
	}  
	
	public function addData($postData){
		
		$data= array(
					'fr_paymode'=>$postData['fr_paymode'],
					'fr_desc'=>$postData['fr_desc'],
					'fr_category'=>$postData['fr_category'],
					'fr_bankslipsts'=>$postData['fr_bankslipsts']
					); 

		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data= array(
					'sa001_address'=>$postData['sa001_address'],
					'sa001_address2'=>$postData['sa001_address2'],
					'sa001_poscode'=>$postData['sa001_poscode'],
					'sa001_state'=>$postData['sa001_state'],
					'sa001_country'=>$postData['sa001_country'],
					'sa001_date'=>$postData['sa001_date']
					);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}