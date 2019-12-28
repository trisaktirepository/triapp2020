<?php
class App_Model_Record_DbTable_AcademicPeriod extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_academic_period';
    protected $_primary = 'ap_id';
	
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
				$select->where("ap_ay_id='".$id."'");
			}
			
			$row = $db->fetchAll($select);
	
		
		
		return $row;
	}
	
	public function getActivePeriod($ptestcode){
		 
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'appl_placement_head'),'b.aph_academic_year=a.ap_intake_id')
	 	->where('ap_usm_date > ?',date('Y-m-d',strtotime(time())))
	 	->where('b.aph_placement_code=?',$ptestcode)
		->order('a.ap_usm_date');
		$row = $db->fetchAll($select);
	
	
	
		return $row;
	}
	public function getDataByIntake($id=0){
		$id = (int)$id;
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from($this->_name);
	
		if($id){
			$select->where("ap_intake_id='".$id."'");
		}
			
		$row = $db->fetchRow($select);
	
	
	
		return $row;
	}
	
	public function getCurrentPeriod($month,$year,$intake){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
			if ($intake!=null)
					$select->where('ap_intake_id=?',$intake);
			if (($month) && ($year)) {
				$select->where("ap_code='".$month."/".$year."'");
				$row = $db->fetchRow($select);
				if (!$row) {
					$select = $db->select()
					->from($this->_name)
					->where('ap_intake_id=?',$intake);
					if($month){
						$select->where("ap_month='".$month."'");
					}
					
					if($year){
						$select->where("ap_year='".$year."'");
					}
					$row = $db->fetchRow($select);
					if (!$row) {
						$select = $db->select()
						->from($this->_name);
						if ($intake!=null) $select->where('ap_intake_id=?',$intake);
												
						if($year){
							$select->where("ap_year='".$year."'");
						}
						$row = $db->fetchRow($select);
					}
				}
			 
			 
			}
			//echo $select;
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