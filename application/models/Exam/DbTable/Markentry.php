<?php


class App_Model_Exam_DbTable_Markentry extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e004_student_course_mark';
	protected $_primary = "id";
		
	public function getMarkentry($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is no Mark Entry Information");
		}
			
		return $row->toArray();
	}
	
	public function getMarkByComponent($rd_id,$component_id,$component_item_id){
		
		$select = $this->select()    	    	
    	               ->from($this->_name);
    	if ($rd_id)             $select->where("rd_id = ?",  $rd_id);
    	if ($component_id)      $select->where("component_id = ?", $component_id);    	
    	if ($component_item_id) $select->where("component_item_id = ?", $component_item_id);    	
    	
    	//echo $select;
        $rowSet = $this->fetchRow($select);  
     
		return  $rowSet;
	}
		
	public function addMarkentry($data){			
		$this->insert($data);
	}
	
	public function updateMarkentry($data,$id){	
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function totalFinalMark($scr_id){
    	
    	$select = $this->select()    	    	
    	               ->from($this->_name);
    	if ($scr_id) $select->where("scr_id = ?", $scr_id);  	
    	
        $rowSet = $this->fetchAll($select);  
        $rs_item =  $rowSet->toArray();
        
        $grand_total=0;
        foreach($rs_item as $item){        	
        	//get weightage
        	$oitem = new App_Model_Exam_DbTable_Asscompitem();
        	$assitem  = $oitem->getAsscomponentitem($item["component_item_id"]);        	
        	
        	$total = ($item["compenent_item_student_mark"]/100)*$assitem["component_item_weightage"];
        	$grand_total = $grand_total+$total; //total mark per course        	
        }
        
		return $grand_total;
    }
    
    
   
	

}

