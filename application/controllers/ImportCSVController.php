<?php

class StudentFinanceTestController extends Zend_Controller_Action {
	
	public function init(){
		//$this->_helper->layout->setLayout('application');
	}
	
	public function indexAction(){
		
		echo "Read CSV";
		
		$row = 1;
		$duplicate =0;
		if (($handle = fopen("/data/apps/triapp/application/controllers/r_slta.csv", "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	
		    	if($row!=1){
			        $num = count($data);
			        
			        //echo "<p> $num fields in line $row: <br /></p>\n";
			        
			        echo "<br />*****************************************************************<br />";
			        if($this->checkSchoolCode($data[0])){
			        	echo "Duplicate <br />";
			        	$duplicate++;
			        }else{
			        	echo " OK <br />";

			        	$data_to_insert = array(
		    				'name'=>$data[1],
							'stateName'=>$data[5],
			        		'state'=>$this->getState($data[3]),
				        	'cityName'=>$data[2],
				        	'city'=>$this->getCity($data[4]),
							'code'=>$data[0]
		    			);
		    			
		    			$this->addData($data_to_insert);
			        }
			        
			        
			    	for ($c=0; $c < $num; $c++) {
			            echo $data[$c] . "<br />\n";
			        }
		        
		    	}
		        $row++;
		    }
		    fclose($handle);
		}
		
		echo "TOTAL DUPLICATE: ".$duplicate;
		
		exit;
	}
	
	public function checkSchoolCode($code){
		
		$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select(array('sm_id','sm_name'))
	                 ->from(array('sm'=>'school_master'))
	                 ->where("REPLACE(sm.sm_school_code, ' ', '' ) = '".str_replace(' ','',$code)."'" );
	    
        $stmt = $db->query($select);
        $row = $stmt->fetch();
        
        if(!$row){
        	return false;
        }else{
        	return true;
        }
	}
	
	function getState($code){
		$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select(array('idState'))
	                 ->from(array('ts'=>'tbl_state'))
	                 ->where("StateCode = ?", $code);
	    
        $stmt = $db->query($select);
        $row = $stmt->fetch();
        
        if(!$row){
        	return false;
        }else{
        	return $row['idState'];
        }
	}
	
	function getCity($code){
		$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select(array('idCity'))
	                 ->from(array('tc'=>'tbl_city'))
	                 ->where("CityCode = ?", $code);
	    
        $stmt = $db->query($select);
        $row = $stmt->fetch();
        
        if(!$row){
        	return false;
        }else{
        	return $row['idCity'];
        }
	}	
	
	public function addData($data){
		$data2 = array(
	    				'sm_name' => $data['name'],
	    				'sm_type' => 0,
	    				'sm_address1' => 'INDONESIA',
	    				'sm_city' => $data['city'],
						'sm_state' => $data['state'],
						'sm_country' => 96,
						'sm_status' => 1,
						'sm_create_date' => date('Y-m-d H:i:s'),
						'sm_create_by' => 1,
						'sm_school_code' =>str_replace(' ','',$data['code'])
	    			);
	    			
	    //$db = Zend_Db_Table::getDefaultAdapter();			
	    //$db->insert('school_master_import', $data2);
	    
	   	
	}
	
	public function updateData($data){
		
	}
}