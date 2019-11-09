<?php

/**
 * @author Ain
 */

class Finance_Model_DbTable_Promotion extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'fr_targetgroup_main';
	protected $_primary = "id";
	
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
						->from($this->_name);
//             		    ->join('sc005_course', 'sc005id = ID_PROG')
//             		    ->join('branch', ' branchID = VENUE');
						
		return $selectData;
	}
	
	public function addPromotion($postData){
		$data = array(
		        'group_code' => $postData['txtcode'],
				'group_desc' => $postData['txtdesc']
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

