<?php
class App_Model_Exam_DbTable_GradeVerification extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e007_grade_verification';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
			$row = $row->toArray();
		}
		
			
		return $row;
	}
	
	public function verifyGrade($request_no){
			$auth = Zend_Auth::getInstance(); 
		
			 $data = array('request_no' => $request_no,
						   'createddt' =>  date("Y-m-d H:i:s"),
						   'createdby' =>  $auth->getIdentity()->id);
					   
			$id=$this->insert($data);
			return $id;
			
	}
	
	public function getInfo($condition=NULL){
		
		$select = $this->select()
					   ->from($this->_name);
	
		if(($condition!=NULL)){
						
			if($condition["group_id"]!='') {
				$select->where('group_id = ?',$condition["group_id"]);
			}
			
			if($condition["request_no"]!='') {
				$select->where('request_no = ?',$condition["request_no"]);
			}
		}
        
		
		$row = $this->fetchRow($select);
	
		return $row;
	}
	
	public function insertData($data){
		$this->insert($data);
	}
	
	public function updateData($data,$id){		
		$this->update($data, 'id='.$id);
	}
	
	public function deleteData($id){		
		$this->delete(' id = ' . (int)$id);
	}
        
	
}