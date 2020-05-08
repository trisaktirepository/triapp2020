<?php

class App_Model_Application_DbTable_PlacementTestComponent extends Zend_Db_Table_Abstract {

	protected $_name = 'appl_component';
	protected $_primary = "ac_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('ac'=>$this->_name))
					->join(array('u'=>'u001_user'),'u.id = ac.ac_update_by', array('ac_update_by_name'=>'fullname'))
					->join(array('att'=>'appl_test_type'),'att.act_id = ac.ac_test_type',array('ac_test_type_name'=>'act_name'))
					->where('ac.ac_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
						->from(array('ac'=>$this->_name))
						->where('ac.ac_status = ?', 1);
								
			$row = $db->fetchAll($select);
		}
		
//		if(!$row){
//			throw new Exception("There is No Student Information");
//		}
		return $row;
		
	}
	public function getDataByComponent($programset,$comp){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
		->distinct()
		->from(array('ac'=>$this->_name),array('ac_comp_name_bahasa','ac_comp_name'))
		->join(array('att'=>'appl_test_type'),'att.act_id = ac.ac_test_type',array('ac_test_type_name'=>'act_name'))
		->join(array('cp'=>'appl_placement_program_setup'),'cp.apps_comp_code=ac.ac_comp_code',array())
		->where('ac.ac_test_type = ?', $comp)
		->where('cp.aph_type=0')
		->where('cp.apps_program_id in ('.$programset.')')
		->order($this->_primary.' ASC');
		//echo $selectData;
		$row=$db->fetchAll($selectData);
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function getComponenByTransaction($trxid,$aph_code){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
		->distinct()
		->from(array('ac'=>$this->_name),array('ac_comp_name_bahasa','ac_comp_name','ac_id'))
		//->join(array('att'=>'appl_test_type'),'att.act_id = ac.ac_test_type',array('ac_test_type_name'=>'act_name'))
		->join(array('cp'=>'appl_placement_program_setup'),'cp.apps_comp_code=ac.ac_comp_code',array())
		->join(array('p'=>'tbl_program'),'p.idprogram=cp.apps_program_id',array())
		->join(array('ap'=>'applicant_program'),'ap.ap_prog_code=p.programcode',array())
		->where('cp.aph_type=?', $aph_code) 
		->where('ap.ap_at_trans_id=?',$trxid);
		echo $selectData;
		$row=$db->fetchAll($selectData);
		echo var_dump($row); 
		return $row;
	}
	public function getDataComponent($id=0,$aphtype=null){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('ac'=>$this->_name)) 
			->where('ac.ac_test_type = '.$id);
		if ($aphtype!=null) $select->where('aph_type=?',$aphtype);
		$row = $db->fetchAll($select);
		 
		return $row;
	
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('ac'=>$this->_name))
						->join(array('att'=>'appl_test_type'),'att.act_id = ac.ac_test_type',array('ac_test_type_name'=>'act_name'))
						->where('ac.ac_status = ?', 1)
             		    ->order($this->_primary.' ASC');
						
		return $selectData;
	}
	
	public function searchPaginate($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('ac'=>$this->_name))
						->join(array('att'=>'appl_test_type'),'att.act_id = ac.ac_test_type',array('ac_test_type_name'=>'act_name'))
						->where("ac.ac_comp_code LIKE '%".$post['ac_comp_code']."%'")
						->where("ac.ac_comp_name LIKE '%".$post['ac_comp_name']."%'")
						->where("ac.ac_comp_name_bahasa LIKE '%".$post['ac_comp_name_bahasa']."%'")
						->where("ac.ac_short_name LIKE '%".$post['ac_short_name']."%'")
						->where("ac.ac_test_type like '%".$post['ac_test_type']."%'")
             		    ->order('ac.'.$this->_primary.' ASC');
						
		return $selectData;
	}
	
	public function addData($postData){
		
		$data = array(
		        'ac_comp_code' => $postData['ac_comp_code'],
				'ac_comp_name' => $postData['ac_comp_name'],
				'ac_comp_name_bahasa' => $postData['ac_comp_name_bahasa'],
				'ac_short_name' => $postData['ac_short_name'],
				'ac_test_type' => $postData['ac_test_type'],
				'ac_update_by' => $postData['ac_update_by'],
				'ac_update_date' => $postData['ac_update_date'],
				'ac_status' => $postData['ac_status']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		
		$data = array(
		        'ac_comp_code' => $postData['ac_comp_code'],
				'ac_comp_name' => $postData['ac_comp_name'],
				'ac_comp_name_bahasa' => $postData['ac_comp_name_bahasa'],
				'ac_short_name' => $postData['ac_short_name'],
				'ac_test_type' => $postData['ac_test_type'],
				'ac_update_by' => $postData['ac_update_by'],
				'ac_update_date' => $postData['ac_update_date']
				);
			
		$this->update($data, 'ac_id = '. (int)$id);
	}
	
	public function deleteData($data,$id){
		if($id!=0){
			$this->update($data, 'ac_id = '. (int)$id);
		}
	}

}

