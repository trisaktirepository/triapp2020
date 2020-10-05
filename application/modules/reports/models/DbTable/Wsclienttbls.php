<?php
//error_reporting(0);
include_once 'Zend/nusoap/nusoap.php';
//include_once 'Zend/nusoap/class.wsdlcache.php';

class Reports_Model_DbTable_Wsclienttbls extends Zend_Db_Table {
	protected $username = '031016' ;
	protected $password = 'usakti1965';//031016_d1kt1' ;
	//protected $wsdl = 'http://103.28.161.75:8082/ws/sandbox.php?wsdl';
	protected $wsdl ;//= 'http://103.28.161.75:8082/ws/live.php?wsdl';
	
	protected $_name = 'mahasiswa';
			//private $lobjDbAdpt;
			
	public function init(){
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select=$this->lobjDbAdpt->select()
		->from("tbl_universitymaster")
		->where("IdUniversity=1");
		$row=$this->lobjDbAdpt->fetchRow($select);	
		if ($row) $this->wsdl=$row['url_feeder'];
		else $this->wsdl='';	
	}
	public function insertmahasiswa($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$data);
		return $db->lastInsertId();
	}
	
	protected $_name2 = 'mahasiswa_pt';
	//private $lobjDbAdpt;
	public function insertmahasiswa_pt($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name2,$data);
		return $db->lastInsertId();
	}
	
	protected $_kmahasiswa = 'kuliah_mahasiswa';
	public function insertkuliahmahasiswa($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_kmahasiswa,$data);
		return $db->lastInsertId();
	}
	
	protected $_kkuliah = 'kelas_kuliah';
	//private $lobjDbAdpt;
	public function insertkelas_kuliah($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_kkuliah,$data);
		return $db->lastInsertId();
	}
	
	protected $_nilai = 'nilai';
	//private $lobjDbAdpt;
	public function insertnilai($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_nilai,$data);
		return $db->lastInsertId();
	}
	
	protected $_ajar_dosen = 'ajar_dosen';
	//private $lobjDbAdpt;
	public function insertajar_dosen($data){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_ajar_dosen,$data);
		return $db->lastInsertId();
	}
		
	public function getmahasiswa(){
		$db = Zend_Db_Table::getDefaultAdapter();
		//$selectMahasiswa = new Zend_Db_Select($db);
		/*$select = $db ->select()
		 ->from(mahasiswa);*/
		$select = $this->select()
		->from(array('m' => 'mahasiswa'),
				array('id_pd','id_sp', 'nm_pd', 'tmpt_lahir','tgl_lahir','jk','stat_pd',
						'id_agama','id_kk','ds_kel','id_wil','a_terima_kps',
						'id_kebutuhan_khusus_ayah','nm_ibu_kandung',
						'id_kebutuhan_khusus_ibu','kewarganegaraan',
						'date_lastinsert','status','date_of_approval'))
						->order("id_pd desc");
		//->limit(0,2);
		//->where('m.status = ?', 'Approved');
		$row = $db->fetchAll($select);
		return $row;
	}
	

	
	public function mhsApprove($formData,$id_pd) {
	$db = Zend_Db_Table::getDefaultAdapter();
	$formData['status']='Approved';
	$formData['date_of_approval']=date('Y-m-d H:i:s');
	//echo"<pre>" ;	print_r ($formData) ;exit();
	$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
	$db = Zend_Db_Table::getDefaultAdapter();
	$select = $this->select()
	->from(array('m' => 'mahasiswa'),
			array('id_pd','id_sp', 'nm_pd', 'tmpt_lahir','tgl_lahir','jk','stat_pd',
					'id_agama','id_kk','ds_kel','id_wil','a_terima_kps',
					'id_kebutuhan_khusus_ayah','nm_ibu_kandung',
					'id_kebutuhan_khusus_ibu','kewarganegaraan',))
					//->order("id_sp desc")
					//->limit(0,2);
	->where('m.status = ?', Approved)
	->where('m.date_sync IS NULL');
	//->where('id_pd='.$id_pd);
	$row = $db->fetchAll($select);
	//echo"<pre>" ;	print_r ($row) ;exit();
	$recorda = $row ;
	$client = new nusoap_client($this->wsdl, true);
	$proxy = $client->getProxy();
	$result = $proxy->GetToken($this->username, $this->password);
	$token = $result ;
	$table = 'mahasiswa';
	$records = $recorda;
	$result = $proxy->InsertRecordset($token, $table, json_encode($records));
	//echo"<pre>" ;	print_r ($result) ;
	unset($result['error_code']);
	unset($result['error_desc']);
	$result2 = array_shift($result);
	$result3 = $result2[0][id_pd];
	$result4 = $result2[0][error_code];
	$result5 = $result2[0][error_desc];
	/*
	print_r ($result3) ;print_r ($result4) ;print_r ($result5) ;
	*/
	//update record mahasiswa
	$db = Zend_Db_Table::getDefaultAdapter();
	$formData['id_pdref']= $result3 ;
	$formData['error_code']= $result4 ;
	$formData['error_desc']= $result5 ;
	//$formData['date_sync']=date('Y-m-d H:i:s');
	$db->update('mahasiswa',$formData,'id_pd='.$id_pd);
	$db = Zend_Db_Table::getDefaultAdapter();
	$formData['id_pd']= $result3 ;
	$formData['date_sync']=date('Y-m-d H:i:s');
	unset($formData['status']);
	unset($formData['id_pdref']);
	unset($formData['date_of_approval']);
	$db->update('mahasiswa_pt',$formData,'id_pdref='.$id_pd);
	//Insert record to WS table Mahasiswa_pt
	$db = Zend_Db_Table::getDefaultAdapter();
	$select = $this->select()
	->from(array('mpt' => 'mahasiswa_pt'),
			array('id_pd','id_sp','id_pd','id_sms','nipd','tgl_masuk_sp','id_jns_daftar'))//->order("id_sp desc")
						//->limit(0,2);
						//->where('m.status = ?', Approved)
				->where('mpt.id_pd IS NOT NULL')
				->where('mpt.date_sync IS NOT NULL')
				->setIntegrityCheck(false); // ADD This Line
							//->where('id_pd='.$id_pd);
				$row2 = $db->fetchAll($select);
							//echo"<pre>" ;	print_r ($row) ;exit();
				$recordb = $row2 ;
				$client = new nusoap_client($this->wsdl, true);
				$proxy = $client->getProxy();
				$resultmpt = $proxy->GetToken($this->username, $this->password);
				$token = $resultmpt ;
				$table = 'mahasiswa_pt';
				$records1 = $recordb;
				$resultmpt = $proxy->InsertRecordset($token, $table, json_encode($records1));
				//echo"<pre>" ;	print_r ($resultmpt) ;
				unset($resultmpt['error_code']);
				unset($resultmpt['error_desc']);
				$resultmpt2 = array_shift($resultmpt);
				$resultmpt3 = $resultmpt2[0][id_reg_pd];
				$resultmpt4 = $resultmpt2[0][error_code];
				$resultmpt5 = $resultmpt2[0][error_desc];
				/*echo"<pre>" ;	
				print_r ($resultmpt3) ;print_r ($resultmpt4) ;print_r ($resultmpt5) ;
				exit();*/
				//insert id_reg_pd from ws to table mahasiswa_pt
				$db = Zend_Db_Table::getDefaultAdapter();
				$formData['id_reg_pdref']= $resultmpt3 ;
				$formData['error_code']= $resultmpt4 ;
				$formData['error_desc']= $resultmpt5 ;
				$formData['date_sync']=date('Y-m-d H:i:s');
				unset($formData['status']);
				unset($formData['id_pdref']);
				unset($formData['date_of_approval']);
				$db->update('mahasiswa_pt',$formData,'id_pdref='.$id_pd);
	}
	
	public function fnSearchmhs($post = array()){ //function to search a particular scheme details
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"mahasiswa"),array("a.*"));
		if(isset($post['field2']) && !empty($post['field2']) ){
			$lstrSelect = $lstrSelect->where("a.nm_pd = ?",$post['field2']);
		}
		if(isset($post['field3']) && !empty($post['field3']) ){
			$lstrSelect = $lstrSelect->where("a.id_pd  LIKE ?", '%'.$post['field3'].'%');
		}

		$lstrSelect	->where("a.status = ".$post["field7"]);
			
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	
	
	function mhsInsertToDb($formData){//function to insert data to database
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ( $formData ['Save'] );
		$table = "mahasiswa";
		$db->insert($table,$formData);
	}
	
	public function fnGetResult(){//function to display all  details in list
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$result = $proxy->GetToken($this->username, $this->password);
		$token = $result ;
		$table = mahasiswa ;
		$filter = $filterdata ;
		$limit = 152 ;
		$order = $orderfilter;
		$offset = offsetfilter;
		$result = $proxy->GetRecordset($token, $table, $filter, $order, $limit, $offset);
		unset($result['error_code']);
		unset($result['error_desc']);
		$larrresult = array_shift($result);
		return $larrresult;
		//echo"<pre>" ;	print_r ($result2) ;exit();
	}
	
	public function fnGetRecord($table,$filter){//function to display all  details in list
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$token = $proxy->GetToken($this->username, $this->password);
		//$token = $result ;
		//$filter = $filter ;
		$larrresult=array();
		if ($token!='') {
			$result = $proxy->GetRecord($token, $table, $filter);
		
			//echo var_dump($result);exit;
			unset($result['error_code']);
			unset($result['error_desc']);
			if ($result)
				$larrresult = array_shift($result);
			else $larrresult=array();
			//$larrresult = array_shift($result);
		}
		return $larrresult;
		//echo"<pre>" ;	print_r ($result2) ;exit();
	}
	
	public function fnGetRecordSet($table,$filter,$limit=null,$offset=null){//function to display all  details in list
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$token = $proxy->GetToken($this->username, $this->password);
		//$token = $result ;
		//$filter = $filter ;
		//echo $filter;exit;
		$result = $proxy->GetRecordset($token, $table, $filter,'',$limit,$offset);
		//echo var_dump($result);exit;
		unset($result['error_code']);
		unset($result['error_desc']);
		if ($result)
			$larrresult = array_shift($result);
		else $larrresult=array();
		return $larrresult;
		//echo"<pre>" ;	print_r ($result2) ;exit();
	}
	
	public function feedSearch($post){ //function to search a particular scheme details
		//$filterdata = "jk = 'L'";
		//$filterdata = "jk ='".$post["jk"]."' OR "."nm_pd like'".$post["nm_pd"]."'%";
		//$filterdata =  "nm_pd = '".$post["nm_pd"]."'"." OR ". "jk ='".$post["jk"]."'" ;
		$filterdata  = (isset($post["filter"]) ? $post['filter'] : null);
		$limitdata 	 = (isset($post["limit"]) ? $post['limit'] : null);
		$offsetdata  = (isset($post["offset"]) ? $post['offset'] : null);
		$orderfilter = (isset($post["order"]) ? $post["order"] : null);
		$tablefilter = (isset($post["table"]) ? $post["table"] : null);
		//$filterdata = "nm_pd = "."'MUHAMAD 3"."'" ;
		//$larrResult	->where("sp.status = ".$post["field7"]);
		//echo $lstrSelect;
		
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$result = $proxy->GetToken($this->username, $this->password);
		$token = $result ;
		$table = $tablefilter ;
		$filter = $filterdata;
		$limit = $limitdata ;
		$order = $orderfilter;
		$offset = $offsetdata;//offsetfilter;
		$result = $proxy->GetRecordset($token, $table, $filter, $order, $limit, $offset);
		unset($result['error_code']);
		unset($result['error_desc']);
		$lstrSelect = array_shift($result);
		//echo"<pre>" ;	print_r ($lstrSelect) ;exit();
		return $lstrSelect;
	}
	
	public function getdict($post){ //function to search a particular scheme details
		$tablefilter = $post["table"];
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$result = $proxy->GetToken($this->username, $this->password);
		$token = $result ;
		$table = $tablefilter;
		$result = $proxy->GetDictionary($token, $table);
		unset($result['error_code']);
		unset($result['error_desc']);
		$lstrSelect = array_shift($result);
		//echo"<pre>" ;	print_r ($lstrSelect) ;exit();
		return $lstrSelect;
	}
	
	public function gettable(){ //function to search a particular scheme details
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$result = $proxy->GetToken($this->username, $this->password);
		$token = $result ;
		$result = $proxy->ListTable($token, $table);
		unset($result['error_code']);
		unset($result['error_desc']);
		$lstrSelect = array_shift($result);
		//echo"<pre>" ;	print_r ($lstrSelect) ;exit();
		return $lstrSelect;
	}
	
	/*
	 * STANDARD FUNCTION TO PUSH DATA TO FEEDER
	 * $table = tablename at feeder
	 * $data  = array containing data to appropriate key to the feeder table
	 */
	
	public function insertToFeeder($table = null, array $data) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$token = $proxy->GetToken($this->username, $this->password);
	
		$result = $proxy->InsertRecord($token, $table, json_encode($data));
	
		return $result;
	}
			
	public function updateToFeeder($table = null, array $key, array $data) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$token = $proxy->GetToken($this->username, $this->password);
	
		$update['key'] = $key;
		$update['data'] = $data;
		
		$result = $proxy->UpdateRecord($token, $table, json_encode($update));
	
		return $result;
	}		
		
	public function deleteToFeeder($table, $key) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$client = new nusoap_client($this->wsdl, true);
		$proxy = $client->getProxy();
		$token = $proxy->GetToken($this->username, $this->password);
	
		$result = $proxy->DeleteRecord($token, $table,json_encode($key));
	
		return $result;
	}
		
}
			
			
			
			
			
			
			

