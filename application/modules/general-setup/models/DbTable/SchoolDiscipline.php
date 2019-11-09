<?php
//require_once 'Zend/Controller/Action.php';
class GeneralSetup_Model_DbTable_SchoolDiscipline extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'school_discipline';
	protected $_primary = "smd_code";
		
	public function getData($code){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('sd'=>$this->_name))
					->joinLeft(array('st'=>'school_type'),'st.st_id = sd.smd_school_type ',array('smd_school_type_name'=>'st_name','smd_school_type_code'=>'st_code'))
					->where("sd.smd_code = '".$code."'");
			
		$row = $db->fetchRow($selectData);				
		return $row;
	}
	
	public function getPaginateData($search=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($search){
			$selectData = $db->select()
					->from(array('sd'=>$this->_name))
					->joinLeft(array('st'=>'school_type'),'st.st_id = sd.smd_school_type ',array('smd_school_type_name'=>'st_name','smd_school_type_code'=>'st_code'))
					->where("sd.smd_code LIKE '%".$search['smd_code']."%'")
					->where("sd.smd_desc LIKE '%".$search['smd_desc']."%'")
					->where("sd.smd_school_type LIKE '%".$search['smd_school_type']."%'")
					->order('sd.smd_desc ASC');	
		}else{
			$selectData = $db->select()
					->from(array('sd'=>$this->_name))
					->joinLeft(array('st'=>'school_type'),'st.st_id = sd.smd_school_type ',array('smd_school_type_name'=>'st_name','smd_school_type_code'=>'st_code'))
					->order('sd.smd_desc ASC');	
		}
			
		return $selectData;
	}
	
		
	public function addData($postData){
		
		$data = array(
		        'smd_code' => $postData['smd_code'],
				'smd_desc' => $postData['smd_desc'],
				'smd_school_type' => $postData['smd_school_type']				
				);
			
		$this->insert($data);
	}		
		

	public function updateData($postData,$code){
		
		$data = array(
		        'smd_code' => $postData['smd_code'],
				'smd_desc' => $postData['smd_desc'],
				'smd_school_type' => $postData['smd_school_type']				
				);
			
		$this->update($data, "smd_code = '".$code."'");
	}
	
	public function deleteData($code=null){
		if($code!=null){
			$this->delete("smd_code = '".$code."'");
		}
	}	
}

