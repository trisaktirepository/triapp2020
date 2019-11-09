<?php
/**
 * @author Suliana
 */

class Finance_Model_DbTable_Feestructure extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'f005_feestructure';
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
	
	public function getFee($courseID=0){
		$courseID = (int)$courseID;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
							->from(array('f'=>$this->_name))
	             		    ->where('f.idCourse = ' .$courseID)
	             		    ->where('f.status = 1')
	             		    ->join(array('c'=>'f004_feecomponent'), 'c.id = f.idComp', array('componentname'=>'c.name'))
	             		    
	             		    ;
		
        $row = $db->fetchRow($selectData);             		    
				
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name));
		
		return $select;
	}
	
	public function addData($postData){
		
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}