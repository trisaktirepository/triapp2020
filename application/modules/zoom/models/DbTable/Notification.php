<?php
class Zoom_Model_DbTable_Notification extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'tbl_zoom_notif';
	protected $_primary = "zoom_id";



	public function getData($id=0){
		$id = (int)$id;

		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}

		if(!$row){
			throw new Exception("There is No Applied Program");
		}

		return $row->toArray();
	}

	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();

		$select = $db->select()
		->from($this->_name)
		->order('id');

		return $select;
	}

	public function checkAdditional($applicantid){
		$db = Zend_Db_Table::getDefaultAdapter();

		$select = $db->select()
		->from($this->_name)
		->where('applicantid = '.$applicantid);

		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $result;
	}

	public function getAdditional($id=0){

		$id = (int)$id;

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.applicantid = '.$id);
		//						->join(array('p'=>'r006_program'),'p.id = a.ARD_PROGRAM',array('program_code'=>'code'))
		//						->join(array('mp'=>'r005_program_main'),'mp.id = p.program_main_id',array('main_name'=>'name'));


		$row = $db->fetchRow($select);


		//		if(!$row){
		//			throw new Exception("There is No Applicant");
		//		}
		//
		return $row;
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

	public function add($data){
	$db = Zend_Db_Table::getDefaultAdapter();
		

	$this->insert($data);
	$id = $db->lastInsertId();

	return $id;
		
	}

	public function updateData($postData,$id){
	$data = array(
			'par_name' => $postData['par_name'],
			'par_kinship' => $postData['par_kinship'],
					'par_occupation' => $postData['par_occupation'],
							'par_placeofwork' => $postData['par_placeofwork'],
									'par_contact' => $postData['par_contact'],
		        'par_address' => $postData['par_address'],
		        'par_email' => $postData['par_email'],
		        'ec_name_1' => $postData['ec_name_1'],
		        'ec_nationality_1' => $postData['ec_nationality_1'],
		        		'ec_kinship_1' => $postData['ec_kinship_1'],
		        				'ec_occupation_1' => $postData['ec_occupation_1'],
		        				'ec_contact_1' => $postData['ec_contact_1'],
		        				'ec_address_1' => $postData['ec_address_1'],
		        				'ec_email_1' => $postData['ec_email_1'],
		        				'ec_name_2' => $postData['ec_name_2'],
		        				'ec_nationality_2' => $postData['ec_nationality_2'],
		        'ec_kinship_2' => $postData['ec_kinship_2'],
		        'ec_occupation_2' => $postData['ec_occupation_2'],
		        'ec_contact_2' => $postData['ec_contact_2'],
		        'ec_address_2' => $postData['ec_address_2'],
		        'ec_email_2' => $postData['ec_email_2']
	);
		
		$this->update($data,'applicantid = '. (int)$id);
	}

	public function deleteData($id){
	$this->delete($this->_primary . ' = ' . (int)$id);
	}

}


