<?php

class App_Model_Application_DbTable_PlacementTest extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'a003_placement_test';
	protected $_primary = "ID";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('place'=>$this->_name))
					->where('place.ID = '.$id)
					->join(array('p'=>'r006_program'),'p.id=place.ID_PROG',array('program_id'=>'id'))
					->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'))
					->join(array('v'=>'g004_branch'),'v.id=place.VENUE',array('branch_name'=>'name'));
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('place'=>$this->_name))
					->join(array('p'=>'r006_program'),'p.id=place.ID_PROG',array('program_id'=>'id'))
					->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'))
					->join(array('v'=>'g004_branch'),'v.id=place.VENUE',array('branch_name'=>'name'));
								
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
	
	
	public function getPlace($id=""){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
						->from(array('placement'=>$this->_name))
						//->join(array('main_program'=>'r005_program_main'),'placement.ID_PROG = main_program.id',array('main_name'=>'name'))
//						->join(array('branch'=>'g004_branch'),'placement.VENUE = branch.id',array('branch'=>'name'))
             		    ->order($this->_primary.' DESC');
        if($id!=""){
			$select->where("placement.ID_PROG = ".$id);
		}

		//echo $select;	
		$stmt = $select->query();
//		$result = $stmt->fetchAll();
		$result = $stmt->fetch();
						
		return $result;
	}
	
	public function getPlace2($id=""){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
						->from(array('placement'=>$this->_name,array('ID'=>'id_place')))
						->join(array('program'=>'r006_program'),'placement.ID_PROG = program.id',array('id_program'=>'id'))
						->join(array('main_program'=>'r005_program_main'),'program.program_main_id = main_program.id',array('main_name'=>'name'))
						->join(array('branch'=>'g004_branch'),'placement.VENUE = branch.id',array('branch'=>'name','id_branch'=>'id' ))
             		    ->order($this->_primary.' DESC');
        if($id!=""){
			$select->where("placement.ID_PROG = ".$id);
		}		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
						
		return $result;
	}
	
	
	public function getData3($table,$cond=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->where($cond);
		//->order($orderby);
//		echo $select;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
			
		return $result;
	}
	
	public function getList($table,$tbljoin,$joincond,$tbljoin2=0,$joincond2=0,$cond){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from($table)
		->join($tbljoin, $joincond)
		->join($tbljoin2, $joincond2)
		//->join($tbljoin3, $joincond3)
		->where($cond);	
		//->order('charging_type');
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		
		return $result;
	}
	
	public function addPlacementtest($postData){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
		        'ID_PROG' => $postData['ID_PROG'],
		        'NAME' => $postData['NAME'],
				'DATE' => $postData['DATE'],
				'TIME' => $postData['TIME'],
				'VENUE' => $postData['VENUE'],
				'createddt' => date("Y-m-d H:i:s"),
      	 		'createdby' => $auth->getIdentity()->id,
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
				'NAME' => $postData['NAME'],
		        'DATE' => $postData['DATE'],
		        'TIME' => $postData['TIME'],
				'VENUE' => $postData['VENUE'],
				'createddt' => date("Y-m-d H:i:s"),
      	 		'createdby' => $auth->getIdentity()->id,
				);
			
		$this->update($data, 'ID = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete('ID =' . (int)$id);
	}

}

