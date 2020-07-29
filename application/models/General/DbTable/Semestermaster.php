<?php 
class App_Model_General_DbTable_Semestermaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_semestermaster';
	protected $_primary = "IdSemesterMaster";
	
	public function fnGetSemestermasterList(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$lstrSelect = $db->select()
				 				 ->from(array("a"=>"tbl_semestermaster"),array("key"=>"a.IdSemesterMaster","value"=>"a.SemesterMainName"))
				 				 ->order("a.SemesterMainStartDate DESC");
		$larrResult = $db->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fngetSemestermainDetails($IdSemesterMaster="") { //Function to get the user details
		if(trim($IdSemesterMaster) == "") {
	
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('a'=>$this->_name))
			->join(array('acy' => 'tbl_academic_year'),'acy.ay_id = a.idacadyear',array("academicYear"=>'ay_code', 'ay_id'=>'ay_id'))
			->order('acy.ay_code DESC')
			->order('a.SemesterCountType DESC')
			->order('a.SemesterFunctionType DESC');
	
			$result = $this->fetchAll($select);
		}else{
	
			$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('a'=>$this->_name))
			->join(array('acy' => 'tbl_academic_year'),'acy.ay_id = a.idacadyear',array("academicYear"=>'ay_code', 'ay_id'=>'ay_id'))
			->where("a.IdSemesterMaster = $IdSemesterMaster")
			->order('a.SemesterMainName');
	
			$result = $this->fetchRow($select);
		}
	
		return $result->toArray();
	}
	
	public function getSemestermasterList(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$lstrSelect = $db->select()
				 				 ->from(array("a"=>"tbl_semestermaster"))
				 				 ->order("a.SemesterMainStartDate DESC");
		$larrResult = $db->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function getCurrentSemester($scheme=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$lstrSelect = $db->select()
						->from(array("a"=>"tbl_semestermaster"))
						->where("SemesterMainStartDate <= ?",  date('Y-m-d'))
         				->where("SemesterMainEndDate >= ?",  date('Y-m-d'))
						->order("a.SemesterMainName");
		if ($scheme!=null) $lstrSelect->where('a.Scheme = ?', $scheme);
		$larrResult = $db->fetchRow($lstrSelect);
		if (!$larrResult) {
			$lstrSelect = $db->select()
			->from(array("a"=>"tbl_semestermaster"))
			
			->where("SemesterMainStartDate <= ?",  date('Y-m-d'))
			->where("SemesterMainEndDate >= ?",  date('Y-m-d'))
			->order("a.SemesterMainName");
			$larrResult = $db->fetchRow($lstrSelect);
		}
		
		return $larrResult;
	}
	
	public function getPreviousSemester($idsemester){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$lstrSelect = $db->select()
		->from(array("a"=>"tbl_semestermaster"))
		->where("a.IdSemesterMaster=?",$idsemester);
		
		$sem=$db->fetchRow($lstrSelect);
		$semesterGasalGenap=$sem['SemesterCountType'];
		$acadid=$sem['idacadyear'];
		//SemesterFuctionType=0
		//IsCountable=1
		if ($semesterGasalGenap=="1") {
			//get previous acadyear and SemesterCountType=2
			$semcode= (int) substr($sem['SemesterMainCode'], 0,4);
			$yearcode=($semcode-1).'/'.$semcode;

			$lstrSelect = $db->select()
			->from(array("a"=>"tbl_academic_year"))
			->where('a.ay_code=?',$yearcode);
			$year=$db->fetchRow($lstrSelect);
			if ($year) {
				$lstrSelect = $db->select()
				->from(array("a"=>"tbl_semestermaster"))
				->where("a.SemesterCountType=2")
				->where("a.SemesterFunctionType=0")
				->where("a.IsCountable=1")
				->where("a.idacadyear=?",$year['ay_id']);
					
				$sem=$db->fetchRow($lstrSelect);
			}
		} else {
			//semesterCountType=1
			$lstrSelect = $db->select()
			->from(array("a"=>"tbl_semestermaster"))
			->where("a.SemesterCountType=1")
			->where("a.SemesterFunctionType=0")
			->where("a.IsCountable=1")
			->where("a.idacadyear=?",$acadid);
			
			$sem=$db->fetchRow($lstrSelect);
		}
		 
	
		return $sem;
	}
	public function getAllSemesterInYearOfCurrentSemester($scheme=null){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$lstrSelect = $db->select()
		->from(array("a"=>"tbl_semestermaster"))
	
		->where("SemesterMainStartDate <= ?",  date('Y-m-d'))
		->where("SemesterMainEndDate >= ?",  date('Y-m-d'))
		->order("a.SemesterMainName");
		if ($scheme!=null) $lstrSelect->where('a.Scheme = ?', $scheme);
		$larrResult = $db->fetchRow($lstrSelect);
		if (!$larrResult) {
			$lstrSelect = $db->select()
			->from(array("a"=>"tbl_semestermaster"))
				
			->where("SemesterMainStartDate <= ?",  date('Y-m-d'))
			->where("SemesterMainEndDate >= ?",  date('Y-m-d'))
			->order("a.SemesterMainName");
			$larrResult = $db->fetchRow($lstrSelect);
		}
		$acadyear=$larrResult['idacadyear'];
		$lstrSelect = $db->select()
		->from(array("a"=>"tbl_semestermaster"))
		->where("a.idacadtear  = ?",  $acadyear)
		->where('a.SemesterFunctionType in ("0","1","6")')
	 	->order("a.SemesterMainName");
		$larrResult = $db->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function getAllSemesterByDate($dates){
		$db = Zend_Db_Table::getDefaultAdapter();
	
	 $lstrSelect = $db->select()
		->from(array("a"=>"tbl_semestermaster"),array('IdSemesterMaster'))
		->where("a.SemesterMainStartDate  <= ?", $dates)
		->where('a.SemesterFunctionType in ("0","1","6")');
		 
		return $lstrSelect;
	}
	
	
	public function getAllSemesterCanDefer($idstd){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$sem=$db->select()
			->from('tbl_studentregsubjects',array('IdSemesterMain'))
			->where('IdStudentRegistration=?',$idstd);
		
		$lstrSelect = $db->select()
		->from(array("a"=>"tbl_semestermaster"))
		->where("a.IdSemesterMaster  not in (".$sem.")")
		->where('a.SemesterFunctionType=0')
		->where('a.IsCountable=1')
		->where('a.SemesterMainEndDate >= CURDATE()');
		$row=$db->fetchAll($lstrSelect);
		
		return $row;
	}
	
	
	public function getCurrentSemesterTA($scheme=null){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$lstrSelect = $db->select()
		->from(array("a"=>"tbl_semestermaster"))
		->where("SemesterMainStartDate <= ?",  date('Y-m-d'))
		->where("SemesterMainEndDate >= ?",  date('Y-m-d'))
		->where("a.IsCountable ='1'")
		->order("a.SemesterMainName");
		if ($scheme!=null) $lstrSelect->where('a.Scheme = ?', $scheme);
		$larrResult = $db->fetchRow($lstrSelect);
		if (!$larrResult) {
			$lstrSelect = $db->select()
			->from(array("a"=>"tbl_semestermaster"))
			->where("SemesterMainStartDate <= ?",  date('Y-m-d'))
			->where("a.IsCountable ='1'")
			->order("a.SemesterMainStartDate DESC");
			$larrResult = $db->fetchRow($lstrSelect);
		}
		return $larrResult;
	}
	
	public function fnGetSemestermaster($semester_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $db->select()
						->from(array("a"=>"tbl_semestermaster"))
						->where('a.IdSemesterMaster = ?',$semester_id)
						->order("a.SemesterMainName");
		
		$larrResult = $db->fetchRow($lstrSelect);
	
		if($larrResult){
			return $larrResult;
		}else{
			return null;
		}
	}
	
	/* List Countable Semester */
	public function getCountableSemester(){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('a'=>'tbl_semestermaster'),array("key"=>"a.IdSemesterMaster","value"=>"a.SemesterMainName"))
		->where('IsCountable=1')
		->order('SemesterMainStartDate DESC');
			
		//echo $select;
	
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getCouselingSemester(){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->distinct()
		->from(array('a'=>'tbl_semestermaster'),array("key"=>"a.IdSemesterMaster","value"=>"a.SemesterMainName"))
		->join(array('ac'=>'tbl_activity_calender'),'ac.IdSemesterMain=a.IdSemesterMaster',array())
		->join(array('dy'=>'tbl_pdpt_daya_tampung'),'dy.IdSemesterMain=a.IdSemesterMaster',array())
		->where('a.IsCountable=1')
		->where('ac.StartDate <= CURDATE() and ac.EndDate>=CURDATE()')
		->orWhere('dy.tgl_awal_kul <= CURDATE() and dy.tgl_akhir_kul>=CURDATE()')
		->order('a.SemesterMainStartDate DESC');
			
		//echo $select;
	
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	/* List Available Semester */
	public function getSemesterCourseRegistration($progid,$scheme,$intake=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 
		
       
        $select = $db->select()
                    ->from(array('sm'=>'tbl_semestermaster'))
                         
                     ->join(array('ac'=>'tbl_activity_calender'),'ac.IdSemesterMain = sm.IdSemesterMaster')
                     ->where('NOW()	BETWEEN TIMESTAMP(ac.StartDate,ac.StartTime) AND TIMESTAMP(ac.EndDate,ac.EndTime)')
                     ->where('ac.idActivity=18') 
                     ->where('ac.IdProgram=?',(int)$progid) 
                     ->group('sm.SemesterMainName');
        
       
     	//echo $select;exit;
		
		$row = $db->fetchAll($select);
		if ($row) {
			foreach ($row as $key=>$value) {
				 
				$select = $db->select()
				->from(array('det'=>'tbl_activity_calender_intake'))
				->where('det.IdActivityCalendar=?',$value['id'])
				->where('det.idActivity=18')
				->where('det.IdIntake=?',$intake);
				
				$rowdetail=$db->fetchRow($select);
				if ($rowdetail) {
					$select = $db->select()
					->from(array('det'=>'tbl_activity_calender_intake'))
					->where('det.IdActivityCalendar=?',$value['id'])
					->where('det.IdIntake=?',$intake)
					->where('det.idActivity=18')
					->where('NOW()	BETWEEN TIMESTAMP(det.StartDate,det.StartTime) AND TIMESTAMP(det.EndDate,det.EndTime)');
					$rowdetail=$db->fetchRow($select);
					if (!$rowdetail) unset($row[$key]);
				}
			}
		}
        
		return $row;
	}
    
    /*Grab all semestermaster id based on academic year*/
    public function getAllSemesterMasterId($academicYear,$semesterCountType)
    {
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                    ->from(array('sm' => 'tbl_semestermaster'))
                    ->where('sm.idacadyear = ?', (int) $academicYear)
                    ->where('sm.SemesterCountType = ?', (int) $semesterCountType)
                    ->where('sm.SemesterFunctionType IN(0,1)');
        
       // echo $select;
        $row = $db->fetchAll($select);
        
        return $row;
    }
    
    /* Validate Semester */
	public function getSemesterCourseRegistrationValidate($progid,$scheme,$semester_id,$intake=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		/*
    	 *  SELECT sm.`IdSemesterMaster` , sm.`SemesterMainName` , ac . *
			FROM `tbl_semestermaster` AS sm
			JOIN tbl_activity_calender AS ac ON ac.IdSemesterMain = sm.`IdSemesterMaster`
			WHERE CURDATE( )
			BETWEEN ac.StartDate
			AND ac.EndDate
			LIMIT 0 , 30
    	 */
		
       
            $select = $db->select()
                              
                             ->from(array('tp'=>'tbl_program'))
                             ->join(array('ac'=>'tbl_activity_calender'),'ac.IdProgram = tp.IdProgram')
							 ->join(array('sm'=>'tbl_semestermaster'),'ac.idsemestermain=sm.IdSemesterMaster')
                             ->where('NOW()	BETWEEN TIMESTAMP(ac.StartDate,ac.StartTime) AND TIMESTAMP(ac.EndDate,ac.EndTime)')
                             ->where('ac.idActivity=18')
                             ->where('ac.IdSemesterMain=?',(int)$semester_id)
                             ->where('ac.IdProgram=?',(int)$progid)
                             //->where('Allowreg=1')
                            // ->where('sm.Scheme=?',(int)$scheme)
                             ->group('sm.SemesterMainName');
       
        
            
       
        
		
		$row = $db->fetchRow($select);
		//echo $select;
		//echo var_dump($row);exit;
		if ($row) {
			//check for detail calendar wheter open or close
			$select = $db->select()
			->from(array('det'=>'tbl_activity_calender_intake'))
			->where('det.IdActivityCalendar=?',$row['id'])
			->where('det.IdIntake=?',$intake);
			 
			$rowdetail=$db->fetchRow($select);
			
			if ($rowdetail && $intake!=null) {
				$select = $db->select()
				->from(array('det'=>'tbl_activity_calender_intake'))
				->where('det.IdActivityCalendar=?',$row['id'])
				->where('det.IdIntake=?',$intake)
				->where('NOW()	BETWEEN TIMESTAMP(det.StartDate,det.StartTime) AND TIMESTAMP(det.EndDate,det.EndTime)');
				$rowdetail=$db->fetchRow($select);
				 
				if (!$rowdetail) return false;
			}
		}
       
        //echo $sql;
		return $row;
	}
	public function getRegulerSemester($idsemester) {
		
		//get semester info
		$sem=$this->fnGetSemestermaster($idsemester);
		$idacdyear=$sem['idacadyear'];
		$semestercounttype=$sem['SemesterCountType'];
		//get sem reguler
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sm' => 'tbl_semestermaster'))
		->where('sm.idacadyear = ?', (int) $idacdyear)
		->where('sm.SemesterCountType = ?', (int) $semestercounttype)
		->where('sm.SemesterFunctionType =0');
		
		// echo $select;
		$row = $db->fetchRow($select);
		
		return $row;
		
	}
	public function getSemester($idsemester) {
	
		 
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sm' => 'tbl_semestermaster'))
		->where('sm.IdSemesterMaster = ?', (int) $idsemester);
	
		// echo $select;
		$row = $db->fetchRow($select);
	
		return $row;
	
	}
}
?>