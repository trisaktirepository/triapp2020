<?php 
class App_Model_General_DbTable_Collegemaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_collegemaster';
	protected $_primary = "IdCollege";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchAll($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			return null;
		}else{
			return $row->toArray();	
		}
	}
	
	public function fngetCollegemasterData($college_id) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		if($college_id!=null){
			$lstrSelect = $lobjDbAdpt->select()
			 				 ->from(array("a"=>"tbl_collegemaster"))
			 				 ->where("a.IdCollege = ".$college_id)
			 				 ->order("a.CollegeName");
					 				  
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		
			return $larrResult;
		
		}else{
			return null;
		}
     }
     
    public function getFullInfoCollege($collegeid) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("cm"=>"tbl_collegemaster"))
		 				 ->joinLeft(array('c'=>'tbl_city'),'c.idCity=cm.City',array('CityName'))
		 				  ->joinLeft(array('s'=>'tbl_state'),'s.idState=cm.State',array('StateName'))
		 				 ->where("cm.IdCollege = ?",$collegeid);
		 				 
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
     }
	
}
?>