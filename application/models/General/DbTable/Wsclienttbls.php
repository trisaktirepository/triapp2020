<?php
//error_reporting(0);
//include_once 'Zend/nusoap/nusoap.php';
//include_once 'Zend/nusoap/class.wsdlcache.php';

require_once('MoodleRest.php');
class App_Model_General_DbTable_Wsclienttbls extends Zend_Db_Table {
 
	//protected $wsdl = 'http://103.28.161.75:8082/ws/sandbox.php?wsdl';
	protected $wsdl;//='http://103.28.161.239' ;//= 'http://103.28.161.75:8082/ws/live.php?wsdl';
	protected $token;//="f7be8ed3623ce40777b179cd7f47dc46";
	//protected $serverurl = "http://www.docinspection.trisakti.ac.id/webservice/soap/server.php?wsdl=1&wstoken=f7be8ed3623ce40777b179cd7f47dc46";
	protected $serverurl; // = "http://103.28.161.239/webservice/soap/server.php?wsdl=1&wstoken=1767c9775385fc2f9e833f1224410996";
	protected $serverurlREST="http://103.28.161.239/webservice/rest/server.php";
	
	public function init(){
		//get ecternal connection propertis
		$db = Zend_Db_Table::getDefaultAdapter();
		$dbExternal=new Moodle_Model_DbTable_External();
		$row=$dbExternal->getDataByCode('docinspection');
		if ($row) {
			$this->serverurl=$row['url'].$row['Token'];
			$this->token=$row['Token'];
			$this->serverurlREST=$row['url_rest'].$row['Token'];
		}
		
	}
	
	public function initExternal($idexternal){
		//get ecternal connection propertis
		$db = Zend_Db_Table::getDefaultAdapter();
		$dbExternal=new Moodle_Model_DbTable_External();
		$row=$dbExternal->getData($idexternal);
		if ($row) {
			$this->serverurl=$row['url'].$row['Token'];
			$this->token=$row['Token'];
			$this->serverurlREST=$row['url_rest'].$row['Token'];
		}
	
	}
	
	public function createUser($params){
		 
		 
		ini_set("soap.wsdl_cache_enabled", "0");
		 
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_user_create_users($params);
			   
		} catch (Exception $e) {
			echo print_r($e);
   			return "0";
		}
		return $resp;
	}
	
	public function getEnrolUser($params,$options=Array()){
			
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_enrol_get_enrolled_users($params,$options);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function enrolUser($params){
			
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->enrol_manual_enrol_users($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function enrolUserREST($params){
			
			
		 
			
		try {
			 
			$MoodleRest = new MoodleRest($this->serverurlREST, $this->token);
				
			$groups = $MoodleRest->request('enrol_manual_enrol_users', $params,MoodleRest::METHOD_POST);
				
			print_r($groups);
		 	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $groups;
	}
	
	
	public function setRole($params){
			
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_role_assign_roles($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function setRoleRest($params){
			
		try {
				 
			$MoodleRest = new MoodleRest($this->serverurlREST, $this->token);
				
			$resp = $MoodleRest->request('core_role_assign_roles', $params,MoodleRest::METHOD_POST);
			 
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function unsetRole($params){
			
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_role_unassign_roles($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	
	public function updateUser($params){
			
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_user_update_users($params);
			
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	
	public function getUser($params){
			
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_user_get_users($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	
	public function createCourse($params){
		 
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_course_create_courses($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function getCourseByField($params,$params2){
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_course_get_courses_by_field($params,$params2);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function getEnrolManualId($params){
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_enrol_get_course_enrolment_methods($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	
	public function getCategoryByField($params){
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_course_get_categories($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	public function updateCourse($params){
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_course_update_courses($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		return $resp;
	}
	
	public function createCategory($params){
			
		ini_set("soap.wsdl_cache_enabled", "0");
			
		try {
			$client = new Zend_Soap_Client($this->serverurl);
			$resp = $client->core_course_create_categories($params);
	
		} catch (Exception $e) {
			echo print_r($e);
			return "0";
		}
		 return $resp;
	}
	
	 
	 
	
	 
		
}
			
			
			
			
			
			
			

