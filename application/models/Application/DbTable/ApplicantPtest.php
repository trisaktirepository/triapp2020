<?php 
class App_Model_Application_DbTable_ApplicantPtest extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_ptest';
	protected $_primary = 'apt_id';
	
	public function getData(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from($this->_name)
					->where('1');										
       
        $row = $db->fetchRow($select);
		return $row;
	}
	
	public function getPtest($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from($this->_name)
					->where('apt_at_trans_id =?', $transaction_id);										
       
        $row = $db->fetchRow($select);
        
        if($row){
        	return $row;	
        }else{
        	return null;
        }
		
	}
	
	public function getUsmPtestCode($transaction_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from($this->_name)
		->where('apt_at_trans_id =?', $transaction_id)
		->where('MID(apt_ptest_code,1,3)="USM"');
		 
		$row = $db->fetchRow($select);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	
	}
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function getInfo($appl_id,$ptest_code){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db ->select()
					->from($this->_name)
					->where("apt_at_trans_id='".$appl_id."'")
					->where("apt_ptest_code='".$ptest_code."'");										
       
        $row = $db->fetchRow($select);
		return $row;
	}
	
	public function updateInfo($data,$transaction_id){
		 $this->update($data, 'apt_at_trans_id  = '. (int)$transaction_id);
	}
	
	/*public function getPlacementTestProgram($transaction_id){
		//query ni kalo pakai salah tau kene guna yg lain
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    echo $select = $db ->select()
					->from(array('ap'=>$this->_name))					
					
					->joinLeft(array('p'=>'tbl_program'),'p.ProgramCode=aprog.app_program_code',array('program_name'=>'p.ProgramName','program_code'=>'p.ProgramCode'))
					->joinLeft(array('d'=>'tbl_departmentmaster'),'p.idFacucltDepartment=d.IdDepartment',array('faculty'=>'d.DeptName'))
					->where("ap.apt_at_trans_id  = '".$transaction_id."'")				
					->order("app.app_preference Asc");
					
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
		return $row;
	}*/
	
	
public function getScheduleInfo($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db ->select()
					->from(array('apt'=>$this->_name))
					->joinLeft(array('aps'=>'appl_placement_schedule'),'aps.aps_id  = apt.apt_aps_id',array('aps_id'=>'aps.aps_id','aps_location_id'=>'aps.aps_location_id','aps_test_date'=>'aps.aps_test_date','aps_start_time'=>'aps.aps_start_time','aps.aps_placement_code'))
					->joinLeft(array('al'=>'appl_location'),'al.al_id=aps.aps_location_id')
					->where("apt_at_trans_id='".$transaction_id."'");														
       
        $row = $db->fetchRow($select);
        //print_r($row);
		return $row;
	}
	
	
	public function generateNoPes($transactionID){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$pdata = $this->getPtest($transactionID);
		$pesno = $pdata["apt_no_pes"];
		
		if($pesno==""){
			
			$sql = "select IntakeId,ap_number,at_period,at_intake from applicant_transaction at 
					inner join tbl_intake as ti on at.at_intake=ti.IdIntake
					inner join tbl_academic_period as ap on ap.ap_id = at.at_period
					where at_trans_id='$transactionID'
			";
			//echo $sql;exit;
			$row = $db->fetchRow($sql);	
			
			//2 angka pertama adalah intake year
			
			$pes[0]=substr($row["IntakeId"],2,2);
			
			
			//Period code mesti berdasarkan schedule date\
			$periodDB = new App_Model_Record_DbTable_AcademicPeriod();
			$sched=$this->getScheduleInfo($transactionID);
			$ptPeriod   = $periodDB->getCurrentPeriod(date("n",strtotime($sched['aps_test_date'])), date("Y",strtotime($sched['aps_test_date'])),$row['at_intake']);
			
			if(strlen($sched["aps_location_id"])==1){
				$pes[1]="0".$sched["aps_location_id"];
			}else{
				$pes[1]=$sched["aps_location_id"];
			}
			/* handle problem application yg period nya
			 * yg base on application/transaction(USM only) date*/
			
			$sqlu ="update applicant_transaction set at_period='".$ptPeriod["ap_id"]."'
					where at_trans_id='".$transactionID."'
			";
			
			$db->query($sqlu);
			
			
			$sql3 = "select count(*)+1 as bil from applicant_ptest ap
					inner join applicant_transaction as at on at.at_trans_id=ap.apt_at_trans_id
					inner join appl_placement_schedule as aps on aps.aps_id  = ap.apt_aps_id
					where ap.apt_no_pes<>'' and at.at_intake=".$row["at_intake"]." 
					and aps.aps_location_id='".$sched["aps_location_id"]."'
					";
					
					
			$rno = 	$db->fetchRow($sql3);
			
			$strno = 5 - strlen($rno["bil"]);
			$frontno="";
			for($i=0;$i<$strno;$i++){
				$frontno .= "0";
			}
			
			$pes[2]=$frontno.$rno["bil"];	
	
			$pesno = implode("",$pes);
			
			//Pastikan no pes yg di generate unique			
			$unique = false;
			while(!$unique){
				$uniqpes = $this->getPtestbyPesno($pesno);
				if(is_array($uniqpes)){
					$unique = false;
					$rno["bil"]=$rno["bil"]+1;
					
					$strno = 5 - strlen(strval($rno["bil"]));	

					$frontno="";
					for($i=0;$i<$strno;$i++){
						$frontno .= "0";
					}
					$pes[4]=$frontno.$rno["bil"];
					$pesno = implode("",$pes);
					echo $strno."<hr>";
				}else{
					$unique = true;
				}
			}
		
			$sql4="UPDATE applicant_ptest set apt_no_pes='$pesno' where apt_at_trans_id='$transactionID'";
			$db->query($sql4);
		}
		return $pesno;

	}	
	
	public function getPtestbyPesno($pesno){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = " select * from applicant_ptest where apt_no_pes='$pesno' limit 0,1";
		$row = 	$db->fetchRow($sql);
		return $row;
	}	
	
	
	public function listCandidate($sch_id,$limit1,$limit2){
		
	$db = Zend_Db_Table::getDefaultAdapter();	   
  	 $sql="SELECT apt_at_trans_id
		FROM `applicant_ptest`
		WHERE `apt_aps_id` =$sch_id
		AND `apt_sit_no` != '' order by apt_id
		LIMIT $limit1 , $limit2";
  	 
  	 
   	 $row = $db->fetchAll($sql);
   	 return $row;
	}
	
	public function tpaExamToAttend($txnId){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db ->select()
					->from(array('apt'=>$this->_name))
					->join(array('aps'=>'appl_placement_schedule'),'aps.aps_id  = apt.apt_aps_id')
					->join(array('aph'=>'appl_placement_head'),'aph.aph_placement_code = apt.apt_ptest_code and aph.aph_testtype = 1')
					->where("apt_at_trans_id='".$txnId."'")
	    			->where("aps.aps_test_date >= '".date("Y-m-d")."'");														
       //echo $select;
        $row = $db->fetchRow($select);
        
        if($row){
			return $row;
        }else{
        	return null;
        }
		
	}
	
	public function getTpaExamSchedule($txnId){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('apt'=>$this->_name))
		->join(array('aps'=>'appl_placement_schedule'),'aps.aps_id  = apt.apt_aps_id')
		->join(array('aph'=>'appl_placement_head'),'aph.aph_placement_code = apt.apt_ptest_code and aph.aph_testtype = 1')
		->join(array('r'=>'appl_room'), 'r.av_id = apt.apt_room_id')
		->where("apt_at_trans_id='".$txnId."'")
		->order("aps.aps_test_date DESC");
		 
		$row = $db->fetchRow($select);
	
		if($row){
			return $row;
		}else{
			return null;
		}
	
	}
	
}
?>