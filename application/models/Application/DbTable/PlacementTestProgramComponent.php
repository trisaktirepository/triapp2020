<?php

class App_Model_Application_DbTable_PlacementTestProgramComponent extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_placement_program_setup';
	protected $_primary = "apps_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('apps'=>$this->_name))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = apps.apps_create_by', array('apps_create_by_name'=>'fName'))
					->where('apps.apps_id = '.$id);

			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('apps'=>$this->_name))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = apps.apps_create_by', array('apps_create_by_name'=>'fName'));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getProgramData($program_id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('apps'=>$this->_name))
				->joinLeft(array('u'=>'tbl_user'),'u.iduser = apps.apps_create_by', array('apps_create_by_name'=>'fName'))
				->joinLeft(array('c'=>'appl_component'),'c.ac_comp_code = apps.apps_comp_code', array('component_name'=>'ac_comp_name','component_name_bahasa'=>'ac_comp_name_bahasa', 'component_code'=>'ac_comp_code'))
				->joinLeft(array('ct'=>'appl_test_type'),'ct.act_id = c.ac_test_type', array('component_type'=>'act_name'))
				->where('apps.apps_program_id = '. $program_id);

		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getPaginateProgram(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('p'=>'tbl_program'))
					->order('p.ProgramName');
						
		return $selectData;
	}
	
	public function searchPaginateProgram($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('p'=>'tbl_program'))
						->where("p.ProgramName LIKE '%".$post['ProgramName']."%'")
						->where("p.ProgramCode LIKE '%".$post['ProgramCode']."%'")
						//->group('al.al_id')
             		    ->order('p.ProgramName');
						
		return $selectData;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('p'=>'tbl_program'))
					->joinLeft(array('apps'=>$this->_name),'apps.apps_program_id = p.idProgram')
					->joinLeft(array('c'=>'appl_component'),'c.ac_comp_code = apps.apps_comp_code', array('component_name'=>'ac_comp_name','component_name_bahasa'=>'ac_comp_name_bahasa'))
					->joinLeft(array('u'=>'tbl_user'),'u.iduser = apps.apps_create_by', array('apps_create_by_name'=>'fName'));
						
		return $selectData;
	}
	
	public function searchPaginate($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('apps'=>$this->_name))
						->joinLeft(array('u'=>'tbl_user'),'u.iduser = apps.apps_create_by', array('apps_create_by_name'=>'fName'))
						//->where("al.al_location_code LIKE '%".$post['al_location_code']."%'")
						//->where("al.al_location_name LIKE '%".$post['al_location_name']."%'")
						//->group('al.al_id')
             		    ->order('apps.apps_comp_code ASC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		$data = array(
		        'apps_program_id' => $postData['apps_program_id'],
				'apps_comp_code' => $postData['apps_comp_code'],
				'apps_create_by' => $postData['apps_create_by'],
				'apps_create_date' => $postData['apps_create_date']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'apps_program_id' => $postData['apps_program_id'],
				'apps_comp_code' => $postData['apps_comp_code']
				);
			
		$this->update($data, 'al_id = '. (int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->delete('apps_id = '. (int)$id);
		}
	}

}

