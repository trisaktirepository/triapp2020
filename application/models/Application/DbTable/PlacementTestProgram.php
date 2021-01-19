<?php

class App_Model_Application_DbTable_PlacementTestProgram extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_placement_program';
	protected $_primary = "app_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('app'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('IdProgram' => 'IdProgram','ProgramName' => 'ProgramName', 'ProgramNameIndonesia' => 'ArabicName'))
					->where('app.app_id = '.$id);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('app'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('IdProgram' => 'IdProgram','ProgramName' => 'ProgramName', 'ProgramNameIndonesia' => 'ArabicName'));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	public function getPlacementtestProgramData($placementestCode, $programCode=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select =  $db->select()
					->from(array('app'=>$this->_name))
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('IdProgram' => 'IdProgram','ProgramName' => 'ProgramName', 'ProgramNameIndonesia' => 'ArabicName'))
					->where("app.app_placement_code = '".$placementestCode."'");
		
		if($programCode!=null){
			$select->where('app.app_program_code =? ',$programCode);
		}
								
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	
	public function getActivePlacementtestProgram(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select =  $db->select()
		->from(array('app'=>$this->_name))
		->join(array('h'=>'appl_placement_head'),'app.app_placement_code=h.aph_placement_code')
		->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode = app.app_program_code', array('IdProgram' => 'IdProgram','ProgramName' => 'ProgramName', 'ProgramNameIndonesia' => 'ArabicName'))
		->where("h.aph_start_date <= CURDATE() and h.aph_end_date >=CURDATE()");
	 	$row = $db->fetchAll($select);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	
	}
	
	public function addData($postData){
		
		$data = array(
		        'app_placement_code' => $postData['app_placement_code'],
				'app_program_code' => $postData['app_program_code'],
				'app_pass_mark' => $postData['app_pass_mark']
				);
				
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'app_placement_code' => $postData['app_placement_code'],
				'app_program_code' => $postData['app_program_code'],
				'app_pass_mark' => $postData['app_pass_mark']
				);
			
		$this->update($data, 'app_id = '. (int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('app_id = '. (int)$id);
		}
	}
	
public function getInfo($placementestCode, $programCode=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select =  $db->select()
					->from(array('app'=>$this->_name))					
					->where("app.app_placement_code = '".$placementestCode."'");
		
		if($programCode!=null){
			$select->where('app.app_program_code =? ',$programCode);
		}
								
		$row = $db->fetchRow($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}

}

