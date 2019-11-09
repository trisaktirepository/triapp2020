<?php 
class App_Model_Record_DbTable_PlacementProgram extends Zend_Db_Table_Abstract
{
    protected $_name = 'appl_placement_program';
	protected $_primary = "app_id";
	
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
	
	
	public function getProgrambyPtestCode($app_placement_code){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
						->from(array('app'=>$this->_name))
						->where("app.app_placement_code = '".$app_placement_code."'")
						->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=app.app_program_code',array('program_name'=>'p.ProgramName'));
						
						
		 $row = $db->fetchAll($select);
		 return $row;
	}
	
	
	
}
?>