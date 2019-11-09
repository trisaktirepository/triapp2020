<?php 
class App_Model_Application_DbTable_Batchagent extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_profile';
	protected $_primary = "appl_id";
	
	function getlookupID($ltype="SEX",$code){
		$sql="select ssd_id from sis_setup_detl where ssd_code='$ltype' and ssd_seq='$code'";
		//echo $sql;
		$row = $this->_db->fetchRow($sql);	
		return $row["ssd_id"];
	}
	
	function getstateID($code){
		$sql="select idState from tbl_state where StateCode='$code'";
		$row = $this->_db->fetchRow($sql);	
		return $row["idState"];		
	}
	
	function getcityID($stateid,$citycode){
		$sql="select idCity from tbl_city where idState='$stateid' and CityCode='$citycode'";
		$row = $this->_db->fetchRow($sql);	
		return $row["idCity"];				
	}
	
	function gethsID($hscode){
		$sql= "SELECT sm_id
			FROM `school_master`
			WHERE `sm_school_code` = '$hscode'";
		$row = $this->_db->fetchRow($sql);	
		return $row["sm_id"];			
	}
	
	function checkapplicant($applicationID){
		
	}
}
?>