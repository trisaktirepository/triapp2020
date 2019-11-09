<?php 
class App_Model_General_DbTable_Department extends Zend_Db_Table_Abstract
{
    protected $_name = 'g008_department';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                 ->from(array('d'=>$this->_name))
	                 ->where('d.'.$this->_primary.' = ' .$id)
	                 ->join(array('f'=>'g005_faculty'),
									"f.id = d.faculty_id",
									array('faculty_name'=>'f.name','faculty_code'=>'f.code'));
					                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                 ->from(array('d'=>$this->_name))
	                 ->join(array('f'=>'g005_faculty'),
									"f.id = d.faculty_id",
									array('faculty_name'=>'f.name','faculty_code'=>'f.code'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db ->select()
							->from(array('d'=>$this->_name))
	                 		->join(array('f'=>'g005_faculty'),
									"f.id = d.faculty_id",
									array('faculty_name'=>'f.name'));
		return $selectData;
	}
	
	public function getDepartmentArray(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
							->from(array('d'=>$this->_name))
	                 		->join(array('f'=>'g005_faculty'),
									"f.id = d.faculty_id",
									array('faculty_name'=>'f.name','faculty_code'=>'f.code'))
							->group('d.faculty_id');
							
		$stmt = $db->query($select);
        $row = $stmt->fetchAll();
	    
        
        $i=0;
        foreach ($row as $faculty){
	    	$row[$i]['department'] = $this->getDepartmentFromFaculty($faculty['faculty_id']);
	    	$i++;	    	
	    }
                
        return $row;
	}
	
	public function getDepartmentFromFaculty($faculty_id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
							->from(array('d'=>$this->_name))
							->join(array('f'=>'g005_faculty'),
									"f.id = d.faculty_id",
									array('faculty_name'=>'f.name')
							);
		if($faculty_id!=0){
			$select->where('faculty_id = ?', $faculty_id);
		}					
							
		$stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
        return $row;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'code' => $data['code'],
			'faculty_id' => $data['faculty_id'],
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'code' => $data['code'],
			'faculty_id' => $data['faculty_id'],
		);
			
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>