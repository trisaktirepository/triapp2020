<?php
class App_Model_Record_DbTable_AcademicYear extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_academic_year';
    protected $_primary = 'ay_id';
	
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
	
	public function getCurrentAcademicYearData(){

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name)
				->where($this->_name.".ay_year_status = 'CURRENT'");
				
			$row = $db->fetchRow($select);
				
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name)
				->order('ay_code ASC');
								
		return $select;
	}
	
    public function addData($formData)
    {
    	$db = Zend_Db_Table::getDefaultAdapter();
        
    	$data = array(
            'ay_code' => $formData['ay_code'],
            'ay_desc' => $formData['ay_desc']
        );
        
        $this->insert($data);
        
        return $db->lastInsertId();
    }

	public function updateData($id,$formData)
    {
        $data = array(
            'ay_code' => $formData['ay_code'],
            'ay_desc' => $formData['ay_desc']
        );
        
        $this->update($data, $this->_primary .' = '. (int)$id);
    }
    
	public function getNextAcademicYearData(){

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name)
				->where($this->_name.".ay_year_status = 'NEXT'");
				
			$row = $db->fetchRow($select);
				
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
}