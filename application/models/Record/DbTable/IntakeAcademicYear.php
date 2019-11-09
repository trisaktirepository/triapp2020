<?php
class App_Model_Record_DbTable_IntakeAcademicYear extends Zend_Db_Table_Abstract
{
    protected $_name = 'intake_academic_year';
    protected $_primary = 'id';
	
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
            'ap_code' => $formData['ap_code'],
            'ap_desc' => $formData['ap_desc']
        );
        
        $this->insert($data);
        
        return $db->lastInsertId();
    }

	public function updateData($id,$formData)
    {
        $data = array(
            'ap_code' => $formData['ap_code'],
            'ap_desc' => $formData['ap_desc']
        );
        
        $this->update($data, $this->_primary .' = '. (int)$id);
    }
    
	public function getDataByAcademicYear($id=0){
			$id = (int)$id;			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
								
			if($id){
				$select->where("acad_year='".$id."'");
			}
			
			$row = $db->fetchAll($select);
	
		
		
		return $row;
	}
	public function getDataByIntake($id=0){
		$id = (int)$id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from($this->_name);
	
		if($id){
			$select->where("IdIntake='".$id."'");
		}
			
		$row = $db->fetchRow($select);
	
	
	
		return $row;
	}
	
	public function getCurrentPeriod($month,$year){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
			if (($month) && ($year)) {
				$select->where("ap_code='".$month."/".$year."'");
			} else {					
				if($month){
					$select->where("ap_month='".$month."'");
				}
				
				if($year){
					$select->where("ap_year='".$year."'");
				}
			}
			 
			$row = $db->fetchRow($select);
	
		return $row;
	}
	
	/**
	 * 
	 * Get previos period list 
	 * @param unknown_type $intake_id
	 * @param unknown_type $period_no
	 * @throws Exception
	 */
	public function getPreviousPeriodData($intake_id, $period_no){
					
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name)
				->where('ap_intake_id =?',$intake_id)
				->where('ap_number <=?',$period_no);
							
		$row = $db->fetchAll($select);
		
		
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
}