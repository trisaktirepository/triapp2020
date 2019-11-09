<?php
//require_once 'Zend/Controller/Action.php';
class GeneralSetup_Model_DbTable_HighSchool extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'school_master';
	protected $_primary = "sm_id";
		
	public function getData($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sm'=>$this->_name))
					->joinLeft(array('st'=>'school_type'),'st.st_id= sm.sm_type')
					->joinLeft(array('tct'=>'tbl_city'),'tct.idCity = sm.sm_city')
					->joinLeft(array('ts'=>'tbl_state'),'ts.idState = sm.sm_state')
					->joinLeft(array('tc'=>'tbl_countries'),'tc.idCountry = sm.sm_country')
					->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = sm.sm_create_by', array('sm_create_by_name'=>'fName'))
					->where("sm.sm_id = ?", (int)$id);
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getPaginateData($search=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($search){
			$selectData = $db->select()
						->from(array('sm'=>$this->_name))
						->joinLeft(array('st'=>'school_type'),'st.st_id= sm.sm_type')
						->joinLeft(array('tct'=>'tbl_city'),'tct.idCity = sm.sm_city')
						->joinLeft(array('ts'=>'tbl_state'),'ts.idState = sm.sm_state')
						->joinLeft(array('tc'=>'tbl_countries'),'tc.idCountry = sm.sm_country')
						->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = sm.sm_create_by', array('sm_create_by_name'=>'fName'))
						->where("sm.sm_status = 1")
						->where("sm.sm_name LIKE '%".$search['sm_name']."%'")
						->where("sm.sm_name_bahasa LIKE '%".$search['sm_name_bahasa']."%'")
						->where("sm.sm_type LIKE '%".$search['sm_type']."%'")
						->where("sm.sm_address1 LIKE '%".$search['sm_address1']."%'")
						->where("sm.sm_address2 LIKE '%".$search['sm_address2']."%'")
						->where("sm.sm_city LIKE '%".$search['sm_city']."%'")
						->where("sm.sm_state LIKE '%".$search['sm_state']."%'")
						->where("sm.sm_country LIKE '%".$search['sm_country']."%'")
						->where("sm.sm_email LIKE '%".$search['sm_email']."%'")
						->where("sm.sm_url LIKE '%".$search['sm_url']."%'")
						->where("sm.sm_phone_o LIKE '%".$search['sm_phone_o']."%'")
						->where("sm.sm_school_code LIKE '%".$search['sm_school_code']."%'")
						->order('sm.sm_name ASC');

		}else{
			$selectData = $db->select()
						->from(array('sm'=>$this->_name))
						->joinLeft(array('st'=>'school_type'),'st.st_id= sm.sm_type')
						->joinLeft(array('tct'=>'tbl_city'),'tct.idCity = sm.sm_city')
						->joinLeft(array('ts'=>'tbl_state'),'ts.idState = sm.sm_state')
						->joinLeft(array('tc'=>'tbl_countries'),'tc.idCountry = sm.sm_country')
						->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = sm.sm_create_by', array('sm_create_by_name'=>'fName'))
						->where("sm.sm_status = 1")
						->order('sm.sm_name ASC');
		}	
		return $selectData;
	}
	
		
	public function addData($postData){
		$auth = Zend_Auth::getInstance();
		
		$data = array(
		        'sm_name' => $postData['sm_name'],
				'sm_type' => $postData['sm_type'],
				'sm_address1' => $postData['sm_address1'],
				'sm_city' => $postData['sm_city'],
				'sm_state' => $postData['sm_state'],
				'sm_country' => $postData['sm_country'],
				'sm_status' => $postData['sm_status'],
				'sm_create_date' => date("Y-m-d H:i:s"),
				'sm_create_by' => $auth->getIdentity()->id,
				'sm_status' => 1				
		);
		
		if( isset($postData['sm_name_bahasa']) && $postData['sm_name_bahasa']!="" ){
			$data['sm_name_bahasa'] = $postData['sm_name_bahasa'];
		}
		
		if( isset($postData['sm_address2']) && $postData['sm_address2']!="" ){
			$data['sm_address2'] = $postData['sm_address2'];
		}
		
		if( isset($postData['sm_email']) && $postData['sm_email']!="" ){
			$data['sm_email'] = $postData['sm_email'];
		}
		
		if( isset($postData['sm_url']) && $postData['sm_url']!="" ){
			$data['sm_url'] = $postData['sm_url'];
		}
		
		if( isset($postData['sm_phone_o']) && $postData['sm_phone_o']!="" ){
			$data['sm_phone_o'] = $postData['sm_phone_o'];
		}
		
		if( isset($postData['sm_school_status']) && $postData['sm_school_status']!="" ){
			$data['sm_school_status'] = $postData['sm_school_status'];
		}
		
		if( isset($postData['sm_school_code']) && $postData['sm_school_code']!="" ){
			$data['sm_school_code'] = $postData['sm_school_code'];
		}
			
		return $this->insert($data);
	}		
		

	public function updateData($postData,$id){
		
		$data = array(
		        'sm_name' => $postData['sm_name'],
				'sm_type' => $postData['sm_type'],
				'sm_address1' => $postData['sm_address1'],
				'sm_city' => $postData['sm_city'],
				'sm_state' => $postData['sm_state'],
				'sm_country' => $postData['sm_country']				
		);
		
		if( isset($postData['sm_name_bahasa']) && $postData['sm_name_bahasa']!="" ){
			$data['sm_name_bahasa'] = $postData['sm_name_bahasa'];
		}
		
		if( isset($postData['sm_address2']) && $postData['sm_address2']!="" ){
			$data['sm_address2'] = $postData['sm_address2'];
		}
		
		if( isset($postData['sm_email']) && $postData['sm_email']!="" ){
			$data['sm_email'] = $postData['sm_email'];
		}
		
		if( isset($postData['sm_url']) && $postData['sm_url']!="" ){
			$data['sm_url'] = $postData['sm_url'];
		}
		
		if( isset($postData['sm_phone_o']) && $postData['sm_phone_o']!="" ){
			$data['sm_phone_o'] = $postData['sm_phone_o'];
		}
		
		if( isset($postData['sm_school_status']) && $postData['sm_school_status']!="" ){
			$data['sm_school_status'] = $postData['sm_school_status'];
		}
		
		if( isset($postData['sm_school_code']) && $postData['sm_school_code']!="" ){
			$data['sm_school_code'] = $postData['sm_school_code'];
		}
			
		$this->update($data, "sm_id = ".(int)$id);
	}
	
	public function deleteData($id){
		if($id!=0){
			$this->update(array('sm_status' =>0),"sm_id = ".(int)$id);
		}
	}	
}

