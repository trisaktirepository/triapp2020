<?php 
class App_Model_General_DbTable_Staffmaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_staffmaster';

    private $lobjDbAdpt;
    
	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}    
	public function fngetStaffDetails() { //Function to get the user details
        $result = $this->fetchAll('Active = 1', 'FirstName ASC');
        return $result;
     }
     
     public function getStaffFullName($idStaff) {
     	$db = Zend_Db_Table::getDefaultAdapter();
     	 
     	$select = $db->select()
     	->from(array("sm"=>"tbl_staffmaster"),array('sm.*','staffname'=>'CONCAT(FirstName," ",SecondName," ",ThirdName)'))
     	->joinLeft(array('front'=>'tbl_definationms'),'sm.FrontSalutation=front.IdDefinition',array('FS'=>'BahasaIndonesia'))
     	->joinLeft(array('back'=>'tbl_definationms'),'sm.BackSalutation=back.IdDefinition',array('BS'=>'BahasaIndonesia'))
     		
     	->where("sm.IdStaff = ?",$idStaff);
     	 
     	$row = $db->fetchRow($select);
     	$name=$row['staffname'];
     	if ($row['BS']!='') $name=$name.', '.$row['BS'];
     	if ($row['FS']!='') $name=$row['FS'].' '.$name;
     	$name = $row['StaffId'].' '.$name;
     	return $name;
     		
     }
      
     
	public function fngetUserStaffDetails($IdCollege) { //Function to get the user details
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_staffmaster"),array("a.*"))
		 				  ->where('a.IdCollege = ?',$IdCollege)
		 				 ->where("a.Active = 1")
		 				 ->order("a.FirstName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     } 
     
	
	/*public function fnSearchStaff1($post = array()) { //Function for searching the user details
		 $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$field7 = "a.Active = ".$post["field7"];
		$lstrSelect = $lobjDbAdpt->select()
			   ->from(array('a' => 'tbl_staffmaster'),array('a.*'))
			   ->where('a.FirstName like "%" ? "%"',$post['field2'])
			   ->where('a.SecondName like  "%" ? "%"',$post['field3'])
			   ->where('a.FullName like  "%" ? "%"',$post['field4'])
			   ->where('a.Email like  "%" ? "%"',$post['field6'])
			   ->where($field7)
			   ->order("a.FirstName");
			   print_r($lstrSelect);die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}*/
	
	public function fnSearchStaff($post = array()) { 
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   $lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_staffmaster"),array("sm.*"))
       								->where('sm.FullName like "%" ? "%"',$post['field4'])
      								->where('sm.FirstName like "%" ? "%"',$post['field2'])
       								->where('sm.SecondName like "%" ? "%"',$post['field3'])
       								->where('sm.Email like "%" ? "%"',$post['field6'])
       								->where("sm.Active = ".$post["field7"]);
		if(isset($post['field5']) && !empty($post['field5'])){
				$lstrSelect = $lstrSelect->where("sm.IdLevel = ?",$post['field5']);
			}								
       	$lstrSelect	->order("sm.FirstName");
       					//echo $lstrSelect;die();
       					
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	 }
	public function fnSearchUserStaff($post = array(),$IdCollege) { 
       $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   $lstrSelect = $lobjDbAdpt->select()
       								->from(array("sm"=>"tbl_staffmaster"),array("sm.*"))
       								->where('sm.FullName like "%" ? "%"',$post['field4'])
      								->where('sm.FirstName like "%" ? "%"',$post['field2'])
       								->where('sm.SecondName like "%" ? "%"',$post['field3'])
       								->where('sm.Email like "%" ? "%"',$post['field6'])
       								->where("sm.Active = ".$post["field7"]);
		if(isset($post['field5']) && !empty($post['field5'])){
				$lstrSelect = $lstrSelect->where("sm.IdLevel = ?",$post['field5']);
			}							
       		$lstrSelect->where('sm.IdCollege = ?',$IdCollege)
       				   ->order("sm.FullName");
       					//echo $lstrSelect;die();
       					
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	 }
	 
	public function fnGetCollegeList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.CollegeName"))
		 				 ->where("a.CollegeType = 0")
		 				 ->where("a.Active = 1")
		 				 ->order("a.CollegeName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetLevelList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_definationms"),array("key"=>"a.idDefinition","value"=>"a.DefinitionDesc"))
		 				 ->join(array("b"=>"tbl_definationtypems"),"a.idDefType = b.idDefType AND defTypeDesc='Levels'")
		 				 ->where("a.Status = 1")
		 				 ->order("a.DefinitionDesc");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetSalutationList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_definationms"),array("key"=>"a.idDefinition","value"=>"a.DefinitionCode"))
		 				 ->join(array("b"=>"tbl_definationtypems"),"a.idDefType = b.idDefType AND defTypeDesc='Salutation'")
		 				 ->where("a.Status = 1")
		 				 ->order("a.DefinitionDesc");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetReligionList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect=$lobjDbAdpt->select()
		                 ->from(array("a"=>"tbl_definationms"),array("key"=>"a.idDefinition","value"=>"a.DefinitionCode"))
		 				 ->join(array("b"=>"tbl_definationtypems"),"a.idDefType = b.idDefType AND defTypeDesc='Religion'")
		 				 ->where("a.Status = 1")
		 				 ->order("a.DefinitionDesc");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);		
		return $larrResult;
	}
	
	
	public function fnGetPOBList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_city"),array("key"=>"a.idCity","value"=>"a.CityName"))		 				
		 				 ->where("a.Active = 1")
		 				 ->group("a.CityName")
		 				 ->order("a.CityName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
   public function fnGetBankList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_bank"),array("key"=>"a.IdBank","value"=>"a.BankName"))		 				
		 				 ->where("a.Active = 1")
		 				 ->order("a.BankName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	
	public function fnGetDepartmentList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_departmentmaster"),array("key"=>"a.IdDepartment","value"=>"a.DepartmentName"))
		 				 ->where("a.Active = 1")
		 				 ->order("a.DepartmentName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetUserDepartmentList($IdCollege){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_departmentmaster"),array("key"=>"a.IdDepartment","value"=>"a.DepartmentName"))
		 				 ->where('a.IdCollege = ?',$IdCollege)
		 				 ->where("a.Active = 1")
		 				 ->order("a.DepartmentName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	 public function fnviewStaffDetails($lintidstafft) { //Function for the view user 
    	//echo $lintidepartment;die();
	 	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("a" => "tbl_staffmaster"),array("a.*"))				
		            	->where("a.IdStaff= ?",$lintidstafft);	
		return $result = $lobjDbAdpt->fetchRow($select);
    }
 	public function fnviewStaffSubjectDetails($lintidstafft) { //Function for the view user 
    	//echo $lintidepartment;die();
	 	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("a" => "tbl_staffsubject"),array("a.*"))				
		            	->where("a.IdStaff= ?",$lintidstafft);	
		return $result = $lobjDbAdpt->fetchAll($select);
    }
    
    
	public function fnGetSubjectList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 //->from(array("a"=>"tbl_subjectmaster"),array("key"=>"a.IdSubject","value"=>"a.SubjectName"))
		 				 ->from(array("a"=>"tbl_subjectmaster"),array("key"=>"a.IdSubject","value"=>"CONCAT_WS(' - ',IFNULL(a.SubjectName,''),IFNULL(a.SubCode,''))"))
						 ->where("a.Active = 1")
		 				 ->order("a.SubjectName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetCollegecode($collegid){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_collegemaster"),array("a.CollegeCode"))
       								->where('a.IdCollege = ?',$collegid)
       								->order("a.CollegeCode");
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;			
	}
	
	
public function fnGenerateCode($iduniversity,$collegecode,$deptid,$lastid){								 
            $page	=  "Staff";
			$db 	= 	Zend_Db_Table::getDefaultAdapter();			
			$select =   $db->select()
					->  from('tbl_config')
					->	where('idUniversity  = ?',$iduniversity);				 
			$result = 	$db->fetchRow($select);		
			$sepr	=	$result[$page.'Separator'];
			$str	=	$page."IdField";	
				
			/*$select =  $db->select()
						 		 -> from(array('a'=>'tbl_departmentmaster'))
						 		 -> join(array('b'=>'tbl_collegemaster'),'b.IdCollege=a.IdCollege','b.ShortName AS CShortName')
						 		 ->	where('a.IdDepartment  = ?',$idDept); 	  				 
			$resultCollage = $db->fetchRow($select);	*/	  
					  
			for($i=1;$i<=4;$i++){
				$check = $result[$str.$i];
				switch ($check){
					case 'Uniqueid':
					  $code		= $lastid;
					  break;
					case 'University':								
					  $code	    = $iduniversity;
					  break;
					case 'Department':					 	
					  $code		   = $deptid;
					  break;
					case 'College':							
					  $code		   = $collegecode;
					  break;					  
					default:
					  break;
				}
				if($i == 1) $accCode 	 =  $code;
				else 		$accCode	.=	$sepr.$code;
			}	 		
		return $accCode;			
			
			
		}	
	
	
	public function fnGetCollege($lstridbranch){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_collegemaster"),array("a.Idhead"))
       								->where('a.IdCollege = ?',$lstridbranch);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	} 
	     
	public function fnGetBranchListofCollege($lintidCollege){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.CollegeName"))
		 				 ->where("a.CollegeType = 1")
		 				 ->where("a.Idhead= ?",$lintidCollege)
		 				 ->where("a.Active = 1")
		 				 ->order("a.CollegeName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnGetLevelsList() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
								 ->from(array('a'=>'tbl_definationms'),array("key"=>"a.idDefinition","value"=>"a.DefinitionDesc"))
								  ->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType')
								  ->where('b.defTypeDesc like ?','Levels')
								  ->order("a.DefinitionDesc");
								  //echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fngetStaffMasterListforDD() {
		$lstselectsql = $this->lobjDbAdpt->select()
						->from(array('staff'=>'tbl_staffmaster'),array('key'=>'IdStaff','value'=>"CONCAT_WS(' ',staff.FirstName,staff.SecondName,staff.ThirdName)"))
						->where('staff.Active = 1')
						->order('staff.FirstName');
		return $this->lobjDbAdpt->fetchAll($lstselectsql); 												
	}
	public function fngetBahasStaffMasterListforDD()
	{
		
		$lstselectsql = $this->lobjDbAdpt->select()
								->from(array('staff'=>'tbl_staffmaster'),array("staff.IdStaff","CONCAT_WS(' ',staff.FirstName,staff.SecondName,staff.ThirdName) as EngName","staff.ArabicName as ArabicName"))
								->where('staff.Active = 1')
								->order('staff.FirstName');
		return $this->lobjDbAdpt->fetchAll($lstselectsql); 						
							
	}
	
	
	public function fnViewTempstaffsubject($lintidstafft){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("tss"=>"tbl_tempstaffsubject"),array("tss.*"))
		 				 ->joinLeft(array("sm"=>"tbl_subjectmaster"),'tss.IdSubject = sm.IdSubject',array("sm.*"))
		 				 ->where('tss.unicode = ?',$lintidstafft)
		 				 ->where('tss.deleteFlag =1');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
		
	public function fnGetStafSubjectDetails($lArrIdStaff,$sessionID) { // Function to edit Purchase order details 			
			$select = $this->select()
							->setIntegrityCheck(false)  	
							->join(array('a' => 'tbl_tempstaffsubject'),array('a.idTempStaffSubject'))
							->where("a.unicode = '$lArrIdStaff'")
							->where("a.sessionId = '$sessionID'");
			$result = $this->fetchAll($select);
			return $result;
		}	
		
	 public function fnDeletestaffsubject($idStaffSubject ) { //Function for Delete Purchase order terms
			$db = Zend_Db_Table::getDefaultAdapter();
			$table = "tbl_staffsubject";
	    	$where = $db->quoteInto('idStaffSubject = ?', $idStaffSubject);
			$db->delete($table, $where);
	    }	
	public function fnDeleteTempStafSubjectdtls($lArrIdSubject,$sessionID) { //Function for Delete Purchase order terms
			$db = Zend_Db_Table::getDefaultAdapter();
			$table = "tbl_tempstaffsubject";
	    	$where = $db->quoteInto('unicode = ?', $lArrIdSubject);
	    	$where = $db->quoteInto('sessionId = ?', $sessionID);
			$db->delete($table, $where);
	    }   

	public function fnDeleteTempStaffSubjectsDetailsBysession($sessionID) { //Function for Delete Purchase order terms
		$db = Zend_Db_Table::getDefaultAdapter();
			$table = "tbl_tempstaffsubject";
	    	$where = $db->quoteInto('sessionId = ?', $sessionID);
			$db->delete($table, $where);
	}
	
	public function getListAcademicStaff($idCollege=null) { 
       $db = Zend_Db_Table::getDefaultAdapter();
       
	   $select = $db->select()
	 				 ->from(array("sm"=>"tbl_staffmaster"))
	 				 ->where("Active = 1")
	 				 ->where("StaffAcademic = 0")
	 				 ->order('sm.FullName');
	 				
	 	if($idCollege){
	 		$select->where("IdCollege = ?",$idCollege);	
	 	}

	 	//echo $select;
		$row = $db->fetchAll($select);
		
		return $row;
     } 
     
     
     public function getAcademicStaff($idCollege=null) { 
       $db = Zend_Db_Table::getDefaultAdapter();
       
	   $select = $db->select()
	 				 ->from(array("sm"=>"tbl_staffmaster"))
	 				 ->joinLeft(array('c'=>'tbl_collegemaster'),'c.IdCollege=sm.IdCollege',array('College'=>'ArabicName'))
	 				 ->where("sm.Active = 1")
	 				 ->where("sm.StaffAcademic = 0")
	 				 ->order('sm.FullName');
	 				
	 	if($idCollege){
	 		$select->where("sm.IdCollege = ?",$idCollege);	
	 	}

	 	//echo $select;
		$row = $db->fetchAll($select);
		
		return $row;
     } 
}
?>