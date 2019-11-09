<?php

class App_Model_Application_DbTable_PlacementTestDistribution extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a012_placement_test_markdistribution';
	protected $_primary = "id";
	
	public function getDatae($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		
		if(!$row){
			throw new Exception("There is No Assessment Component");
		}			
		return $row->toArray();
	}
		
	public function getData($id=0){
		
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			echo $select = $db->select()
					->from(array('place'=>$this->_name))
					->where($this->_primary .' = '. $id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('place'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Student Information");
//		}
		return $row;
		
	}
	
	public function getCount($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					->from(array('place'=>$this->_name))
					->where('place.placement_id = '.$id);
					
        $row = $this->getAdapter()->query($select)->rowCount();
		return $row;
	}
	
	
	public function getDataMark($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('place'=>$this->_name))
					->where('place.placement_id = '.$id);
							
			$row = $db->fetchAll($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('place'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Student Information");
//		}
		return $row;
		
	}
	
	
	public function getPaginatePlacementtest(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('program'=>$this->_name))
						->join(array('main_program'=>'r005_program_main'),'program.ID_PROG = main_program.id',array('main_name'=>'name'))
						->join(array('branch'=>'g004_branch'),'program.VENUE = branch.id',array('branch'=>'name'))
             		    ->order($this->_primary.' DESC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		
		$data = array(
		        'program_id'  => $postData['program_id'],
		        'placement_id'   => $postData['placement_id'],				
				'component_name'  => $postData['component_name'],
				'component_weightage' => $postData['component_weightage'],
		        'component_total_mark'    => $postData['component_total_mark']	
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'component_name'  => $postData['component_name'],
				'component_weightage' => $postData['component_weightage'],
		        'component_total_mark'    => $postData['component_total_mark']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete('id =' . (int)$id);
	}

}

