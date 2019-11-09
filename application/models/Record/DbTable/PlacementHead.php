<?php 
class App_Model_Record_DbTable_PlacementHead extends Zend_Db_Table_Abstract
{
    protected $_name = 'appl_placement_head';
	protected $_primary = "aph_id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db ->select()
						->from(array('program'=>$this->_name))
						->where('program.'.$this->_primary.' = ' .$id);

	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
        
		}else{
			$select = $db ->select()
						->from(array('program'=>$this->_name));

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		}
		
		return $row;
	}
	
	
	public function getPlacementTest($aphtype=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db ->select()
						->from($this->_name)
						->join(array('itk'=>'tbl_intake'), 'itk.IdIntake = aph_academic_year', array())
		 				->where('aph_testtype = ?',$aphtype)
						->where("curdate() between itk.ApplicationStartDate and itk.ApplicationEndDate"); 
		 
		 
		 $row = $db->fetchRow($select);
		 return $row;
	}
	
	public function getPlacementTestByCode($pcode){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from($this->_name)
		//->join(array('itk'=>'tbl_intake'), 'itk.IdIntake = aph_academic_year', array())
		->where('aph_placement_code = ?',$pcode);
		//->where("curdate() between itk.ApplicationStartDate and itk.ApplicationEndDate");
			
			
		$row = $db->fetchRow($select);
		return $row;
	}
	
	
	
}
?>