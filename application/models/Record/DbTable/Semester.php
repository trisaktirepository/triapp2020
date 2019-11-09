<?php
class App_Model_Record_DbTable_Semester extends Zend_Db_Table_Abstract
{
    protected $_name = 'r001_semester';
    protected $_primary = 'id';
	
    protected $_referenceMap = array (
		
    );
    
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name)
					->where($this->_name.".".$this->_primary .' = '. $id);
					
				$row = $db->fetchRow($select);
		}else{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
								
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name)
				->order('start_date ASC')
				->order('name DESC');
								
		return $select;
	}
	
    public function addSemester($formData)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
        
    	$data = array(
            'code' => $formData['code'],
            'name' => $formData['name'],
            'year' => $formData['year'],
            'start_date' => $formData['start_date'],
        	'end_date' => $formData['end_date']
        );
        
        $this->insert($data);
        
        return $db->lastInsertId();
    }
    
 //mardhiyati 21 Feb 2011
    public function listSemester()
    {
        $select = $this->select()->from($this, array('id', 'name'));
        $rowSet = $this->fetchAll($select);
		$arraySet = $rowSet->toArray();
		$list = array();
		$list = array("Please Select..");
		foreach ($arraySet as $value) {
			$list[$value['id']] = $value['name'];
		}
        return $list;
    }
    
	public function updateData($semesterID,$formData)
    {
        $data = array(
            'code' => $formData['code'],
            'name' => $formData['name'],
            'start_date' => $formData['start_date'],
        	'end_date' => $formData['end_date']
        );
        
        $this->update($data, $this->_primary .' = '. (int)$semesterID);
    }
    
    public function deleteSemester($semesterID)
    {
        
        //delete semester_program
        $semester_programDB = new App_Model_Record_DbTable_SemesterProgram();
        $semester_programDB->deleteSemesterProgram($semesterID);
        
        //delete semester
        $this->delete('id =' . (int)$semesterID);
    }
    
    
    public function currentSemester(){
    	    $db = Zend_Db_Table::getDefaultAdapter();
    	    $curdate = new Zend_Db_Expr('CURDATE()');
    	
    		$selectIn = $db->select()    	    	
    	               ->from($this->_name,array('id'=>'id'))
    	   			   ->where("start_date <= ?", $curdate)  
    	               ->where("end_date >= ?", $curdate);   

    	    $select = $db->select()
					->from($this->_name)
					->where('id IN (?)',$selectIn);
								
			//$row = $db->fetchAll($select);
			$row = $db->fetchRow($select);
    
			return  $row;   	
    }
}