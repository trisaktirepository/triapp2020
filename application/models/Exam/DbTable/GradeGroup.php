<?php

class App_Model_Exam_DbTable_GradeGroup extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e011_grade_group';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Grade Group");
		}			
		return $row->toArray();
	}
	
	
	public function search($id=0){
		$id = (int)$id;
		
		$select = $this->select()
					 ->from($this->_name)
					 ->where('grade_verification_id IS NOT NULL');        
					 
		if ($id){  
			$select->where('id = ?',$id);
		
			$row = $this->fetchRow($select);
		}else{
			$row = $this->fetchAll($select);
		}	        
					 
		return $row->toArray();
	}
	
	
	public function addgroup($postData){
		$auth = Zend_Auth::getInstance(); 
			
		$data = array(		        
				'group_name'   => $postData['group_name'],				
				'createdby'    => $auth->getIdentity()->id,
		 		'createddt'    => date("Y-m-d H:i:s")
				);
		
		$id=$this->insert($data);
		return $id;
	}
	
	
	public function updategroup($postData,$id){
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(		       
				'group_name'   => $postData['group_name'],	
				'modifiedby'   => $auth->getIdentity()->id,
		 		'modifieddt'   => date("Y-m-d H:i:s")
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	public function deletedata($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	
	public function verifyGrade($grade_group_id,$grade_verification_id){		
		//update
		$info = array ('grade_verification_id'=>$grade_verification_id);
		$this->update($info, $this->_primary .' = '. (int)$grade_group_id);
		
	}
	
	//to set group as university grade
	public function updategrouptype($data,$grade_group_id){
		$this->update($data, $this->_primary .' = '. (int)$grade_group_id);
	} 
	
	
	public function get_info($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		$select = $db->select()
				     ->from(array('g'=>$this->_name))		       
			         ->joinLeft(array('gv'=>'e007_grade_verification'),'g.id=gv.group_id',array('gv_status'=>'gv.status','gv_createddt'=>'gv.createddt','gv_createdby'=>'gv.createdby'));
				
			        //echo $select;  
	
		if($condition!=null){
			
			if($condition["id"]!=''){
				$select->where('g.id ='.$condition["id"]);
			}
			
			if($condition["group_type"]!=''){
				$select->where('g.group_type=1');
			}
		}
		
        //echo $select;
        
		$row = $db->fetchRow($select);	
		return $row;	
	}
	
	
	public function updateData($data,$id){
		$this->update($data, $this->_primary .' = '. (int)$id);
	} 
	
	public function getGroupList(){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
				        
		$select = $db->select()
				     ->from(array('g'=>$this->_name))		       
			         ->joinLeft(array('gv'=>'e007_grade_verification'),'g.id=gv.group_id',array('gv_status'=>'gv.status','gv_createddt'=>'gv.createddt','gv_createdby'=>'gv.createdby'));
				
			        //echo $select;  
				      
		$result = $db->fetchAll($select); 
      
      	return $result;		
	}
	
	
	public function getListGrade(){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
				        
		$select = $db->select()
				     ->from(array('gg'=>$this->_name))		       
			         ->joinLeft(array('g'=>'e001_grade'),'g.grade_group_id=gg.id')
			         ->where('gg.status=1'); //ini verify.
				
	  		      
		$result = $db->fetchAll($select); 
		
		
	}
	
	
	
}