<?php 
class App_Model_Discipline_DbTable_StudentCaseDetail extends Zend_Db_Table_Abstract
{
    protected $_name = 'd004_student_case_detail';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){
	                
			$select = $db->select()
	                ->from($this->_name)
	                ->where($this->_primary.' = ' .$id);	                
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from($this->_name);
		
		return $select;
	}
	
	public function addData($data){	
		$this->insert($data);
	}
	
	public function updateData($data,$id){				
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function search($condition){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				     ->from($this->_name);
			
		if($condition!=null){			
			if($condition['student_case_id']!=""){
				$select->where("student_case_id = ?".$condition['student_case_id']);				
			}
		}	
		
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	
	public function getNoIncident($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				     ->from($this->_name,'COUNT(*) AS total')
				     ->where("student_case_id =".$id);		
		
		$row = $db->fetchRow($select);
		 
		return $row["total"];
	}
	
	public function getStudentCaseDetail($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select  = $db->select()                    
                    ->from(array('scd'=>$this->_name))                    
                    ->joinleft(array('c'=>'d001_case'),'c.id = scd.case_id',array('case_name'=>'c.case_name'))	                     
                    ->joinleft(array('p'=>'d002_penalty'),'p.id = scd.penalty_id',array('penalty_name'=>'p.penalty_name'))
                    ->order('scd.createddt desc')                     
                    ->limit(1,0);	                     
   
    	if ($id)  $select->where('scd.student_case_id= ?',$id); 			
		
    	//echo $select;
		$row = $db->fetchRow($select);
		

		return $row;
	}
}
?>