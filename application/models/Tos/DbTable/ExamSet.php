<?php 
class App_Model_Tos_DbTable_ExamSet extends Zend_Db_Table_Abstract
{
    protected $_name = 'q016_exam_set';
    protected $_subname = 'q017_exam_set_bank';
	protected $_primary = "id";
	protected $_subprimary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('tm'=>$this->_name))
	                 ->where('tm.'.$this->_primary.' = ' .$id)
	                 ->joinLeft(array('c'=>'r010_course'),'c.id=tm.courseid',array('coursename'=>'c.name'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		
		return $row;
	}
	
	
	public function getSubdata($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
			$select = $db->select()
	                 ->from(array('sb'=>$this->_subname))
	                 ->where('sb.exam_set_id='.$id)
	                 ->joinLeft(array('p'=>'q001_pool'),'p.id=sb.pool_id',array('bank_id'=>'p.id','bname'=>'p.name'));

	             
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	      //  $row = $row->toArray();
		
		return $row;
	}
	
	
	
	public function getInfo($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('tm'=>$this->_name))
	                 ->where('tm.'.$this->_primary.' = ' .$id);
	                
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db->select()
	                 ->from($this->_name);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		
		return $row;
	}
	
	public function getSetByCourse($courseid){
		
		  $db = Zend_Db_Table::getDefaultAdapter();
			
		  $select = $db->select()
	                   ->from(array('s'=>$this->_name))
	                   ->where('s.courseid = '.$courseid)
	                   ->joinLeft(array('sb'=>$this->_subname),'sb.exam_set_id=s.id',array('exam_set_id'=>'sb.exam_set_id','pool_id'=>'sb.pool_id'))
	                   ->where('s.status=1'); //active                
			                     
         $stmt = $db->query($select);
         $rows  = $stmt->fetchAll();
         return $rows;
	}
	
	
	
	
	public function getPaginateData($condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
						  ->from(array('s'=>$this->_name))
						  ->join(array('c'=>'r010_course'),'c.id=s.courseid',array('ccode'=>'c.code','cname'=>'c.name'));
						  
		if($condition!=""){
			if($condition['courseid']!=""){
				$selectData->where('courseid='.$condition['courseid']);
			}
			
			if($condition['setname']!=""){
				$selectData->where('setname LIKE %'.$condition['setname'].'%');
			}
		}
		
		return $selectData;
	}
	
	public function addData($data){		
		return $id = $this->insert($data);
	}
	
	public function addDetails($data){	
		$db = Zend_Db_Table::getDefaultAdapter();	
		return $id = $db->insert($this->_subname,$data);
	}
	
	public function updateData($data,$id){		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function updateDetails($data,$id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->update($this->_subname,$data,$this->_subprimary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function deleteDetails($id){
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->delete($this->_subname,'exam_set_id =' . (int)$id);
	}
}
?>