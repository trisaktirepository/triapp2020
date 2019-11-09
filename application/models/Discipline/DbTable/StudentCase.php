<?php 
class App_Model_Discipline_DbTable_StudentCase extends Zend_Db_Table_Abstract
{
    protected $_name = 'd003_student_case';
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
		
		$select  = $db->select()
                    ->from(array('sc'=>$this->_name)) ;        	
		return $select;
	}
	
	public function addData($data){	
		$id = $this->insert($data);
		
		return $id;
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
			if($condition['keyword']!=""){
				$select->where("student_name like '%" .$condition['keyword']."%'");
				$select->Orwhere("student_icno like '%" .$condition['keyword']."%'");
			}
		}	
		
		$row = $db->fetchAll($select);
		 
		return $row;
	}
	
	
	public function getNoIncident($icno){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				     ->from($this->_name,'COUNT(*) AS total')
				     ->where("student_icno =".$icno);		
		
		$row = $db->fetchRow($select);
		 
		return $row["total"];
	}
	
	
	public function getByICNo($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                 ->from($this->_name)
			         ->where("student_icno=".$id);	
		
		$row = $db->fetchRow($select);
	      
		
		return $row;
	}	
	
	public function getStudentCaseDetail($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select  = $db->select()
                    ->from(array('sc'=>$this->_name))
                    ->join(array('scd'=>'d004_student_case_detail'),'scd.student_case_id = sc.id')                    
                    ->joinleft(array('c'=>'d001_case'),'c.id = scd.case_id',array('case_name'=>'c.case_name'))	                     
                    ->joinleft(array('p'=>'d002_penalty'),'p.id = scd.penalty_id',array('penalty_name'=>'p.penalty_name'))
                    ->order('scd.createddt desc');	                     
   
    	if ($id)  $select->where('sc.id= ?',$id); 			
		
    	//echo $select;
		$row = $db->fetchAll($select);
		

		return $row;
	}
	
	//suliana added to check disciplinary case during online application
	public function getAlow($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                 ->from($this->_name)
			         ->where("student_icno=".$id)
			         ->where("case_status != 1");	
		
		$row = $db->fetchRow($select);
	      
		
		return $row;
	}	
	
	public function search_studentCase($condition){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				     ->from($this->_name);
			
		if($condition!=null){			
			if($condition['keyword']!=""){
				$select->where("student_name like '%" .$condition['keyword']."%'");
				$select->Orwhere("student_icno like '%" .$condition['keyword']."%'");
			}
		}	
		
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	public function searchIC($student_ic){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				     ->from($this->_name)
					 ->Orwhere("student_icno like '%" .$student_ic."%'");
		
		$row = $db->fetchRow($select);
		 
		return $row;
	}
}
?>