<?php 
class App_Model_Application_DbTable_ApplicantCompMark extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_ptest_comp_mark';
	protected $_primary = "apcm_id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->where('a.apcm_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('apcm'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}	
	
	public function addData($fdata){
		
		$this->insert($fdata);
	}

	public function updateData($fdata, $apcm_id){
		$this->update($fdata, 'apcm_id = '.$apcm_id);
	}
	
	public function getCompMarkByType($txnID,$compType){
		 
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "Select ap.*,ac.ac_comp_name_bahasa,apcm.apcm_mark as mark from appl_placement_detl ap 
				inner join appl_component as ac 
				on ap.apd_comp_code = ac.ac_comp_code
				inner join ".$this->_name." as apcm
				on apcm.apcm_apd_id = ap.apd_id
				where ac.ac_test_type=$compType
				AND apcm.apcm_at_trans_id = $txnID
				";
							
		$row = $db->fetchAll($sql);
		
		if($row){
			return $row;	
		}else{
			return null;
		}
		
	}
	
	public function getCompMark($txnId,$compId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = " select apcm_mark from ".$this->_name. " 
				Where apcm_apd_id=$compId AND apcm_at_trans_id=$txnId
				";
		$row = $db->fetchrow($sql);
		if($row){
			return $row["apcm_mark"];	
		}else{			
			return null;
		}		
	}

	public function getCompMarkByProg($txnId,$compId,$progcode){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = " select apcm_mark from ".$this->_name. " 
				Where apcm_apd_id=$compId AND apcm_at_trans_id=$txnId 
				And apcm_prog_code='$progcode';
				";
		$row = $db->fetchrow($sql);
		if($row){
			return $row["apcm_mark"];	
		}else{
			//utk handle yang mark dulu x ikut markah
			$rowNoProg=$this->getCompMark($txnId,$compId);
			//echo "masuk";
			if($rowNoProg){
				return $rowNoProg;	
				
			}else{		
				return null;
			}
		}		
	}
	
	public function getCompMarkArray($txnId,$compId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = " select * from ".$this->_name. " 
				Where apcm_apd_id=$compId AND apcm_at_trans_id=$txnId
				";

		$row = $db->fetchrow($sql);
		if($row){
			return $row;	
		}else{
			return null;
		}		
	}
	
	public function getCompByProgram($progcode,$ptestcode,$txnid,$apid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql ="select * from  appl_placement_program
				where app_program_code='$progcode' and  app_placement_code='$ptestcode'
				";
		//echo $sql;
		$row1 = $db->fetchrow($sql);
		
		$sql2 ="select apw.*,ac.ac_comp_name_bahasa from appl_placement_weightage apw
				inner join appl_placement_detl as apd
				on apw.apw_apd_id = apd.apd_id
				inner join appl_component as ac 
				on apd.apd_comp_code = ac.ac_comp_code								
				where apw_app_id='".$row1["app_id"]."' 
				";
		
		$row2 = $db->fetchAll($sql2);
		$total=0;
		foreach($row2 as $key=>$comp){
			$mark=$this->getCompMarkByProg($txnid,$comp['apw_apd_id'],$progcode);
			$weighmark = $mark * $comp['apw_weightage'] /100; 
			$row2[$key]["mark"]=$weighmark;
			$total = $total+$weighmark;
		}
		
		$apdata["ap_usm_mark"]=$total;		
		$approg = new App_Model_Application_DbTable_ApplicantProgram();
		/*$aptest = new App_Model_Application_DbTable_ApplicantPtest();
		$aptdata["apt_usm_attendance"]=1;	
		$aptest->updateInfo($aptdata,$txnid);*/	
       	$approg->updateData($apdata,$apid);
       
		
       	return $row2;
       	/*echo "<pre>";	
       	print_r($row2);
       	echo "</pre>";*/
	}
	public function getCompMarkProg($txnId,$compId,$progcode){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = " select apcm_mark,apcm_id from ".$this->_name. " 
				Where apcm_apd_id=$compId and apcm_prog_code='$progcode' AND apcm_at_trans_id=$txnId
				";
		//echo $sql;
		$row = $db->fetchrow($sql);
		if($row){
			return $row;	
		}else{
			return null;
		}		
	}

public function getMarkEntryByProgram($progcode,$ptestcode,$txnid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql ="select app.*,aph_testtype from  appl_placement_program app
				inner join appl_placement_head as aph on app_placement_code=aph_placement_code
				where app_program_code='$progcode' and  app_placement_code='$ptestcode'
				";
		//echo $sql;exit;
		$row1 = $db->fetchrow($sql);
		
		if($row1){
			$sql2 ="select apw.*,ac.ac_comp_name_bahasa from appl_placement_weightage apw
					inner join appl_placement_detl as apd
					on apw.apw_apd_id = apd.apd_id
					inner join appl_component as ac 
					on apd.apd_comp_code = ac.ac_comp_code								
					where ac_test_type<>1 and apw_app_id=".$row1["app_id"]." 
					";	
			if ($row1["aph_testtype"]==1) {
				$sql2 .= " and ac.ac_comp_code ='TPAP'";
			}
			//echo $sql2;
			$row2 = $db->fetchAll($sql2);
			if($row2){
			foreach($row2 as $key=>$comp){
				$mark=$this->getCompMarkProg($txnid,$comp['apw_apd_id'],$progcode);
				if($mark){
					$row2[$key]["mark"]=$mark["apcm_mark"];
					$row2[$key]["markid"]=$mark["apcm_id"];
				}else{
					$row2[$key]["mark"]=$mark;
					$row2[$key]["markid"]=$mark;		
				}
			}
			
			return $row2;
		}else{
			return null;
		}
		}else{
			return null;
		}
		
	}	
	
	public function getMarkEntryByComp($component,$ptestcode,$txnid){
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$select=$db->select()
			->from($this->_name)
			->where('apcm_at_trans_id=?',$txnid)
			->where('apcm_apd_id=?',$component)
			->where('pcode=?',$ptestcode);
		
		$row =$db->fetchRow($select);
		if (!$row) $row=array('apcm_mark'=>'','apcm_id'=>'');
		return $row;
	}
	
	 
}
?>