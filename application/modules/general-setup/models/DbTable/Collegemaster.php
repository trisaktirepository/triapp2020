<?php
class GeneralSetup_Model_DbTable_Collegemaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_collegemaster';

    public function fnaddCollege($larrformData,$idUniversity,$CodeType,$objIC)  { //Function for adding the University details to the table

    	$larrformData['Phone1'] = $larrformData['Phone1countrycode']."-".$larrformData['Phone1statecode']."-".$larrformData['Phone1'];
		unset($larrformData['Phone1countrycode']);
		unset($larrformData['Phone1statecode']);

		$larrformData['Phone2'] = $larrformData['Phone2countrycode']."-".$larrformData['Phone2statecode']."-".$larrformData['Phone2'];
		unset($larrformData['Phone2countrycode']);
		unset($larrformData['Phone2statecode']);

		$larrformData['Fax'] = $larrformData['Faxcountrycode']."-".$larrformData['Faxstatecode']."-".$larrformData['Fax'];
		unset($larrformData['Faxcountrycode']);
		unset($larrformData['Faxstatecode']);

		unset($larrformData['IdStaff']);
		unset($larrformData['FromDate']);

   	   	if($larrformData ['CollegeType'] == '0' || $larrformData ['CollegeType'] == '1') {
	    	$larrformData ['Idhead']  = '0';
    	}

    	$collegeId =  $this->insert($larrformData);
		 if($CodeType == 1){
			$CollegeCode = $objIC->fnGenerateCode($idUniversity,$collegeId,$larrformData['ShortName'],'College');
			$formData1['CollegeCode'] = $CollegeCode;
			$this->fnupdateCollegeCode($formData1,$collegeId);
		}
		return $collegeId;
	}

	public function fnupdateCollegeCode($formData,$collegeId) { //Function for updating the university
		$where = 'IdCollege = '.$collegeId;
		$this->update($formData,$where);
    }
     public function fngetCollegemasterDetails() { //Function to get the user details
        $result = $this->fetchAll('Active = 1', 'CollegeName ASC');
        return $result;
     }

     public function fngetCollegemasterDetailsById($collegeid) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("a.*"))
		 				 ->where("a.IdCollege = ?",$collegeid)
		 				 ->where("a.Active = 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }

   public function fneditCollege($IdCollege) { //Function for the view University
	$select = $this->select()
			->setIntegrityCheck(false)
			->join(array('col' => 'tbl_collegemaster'),array('col.IdCollege'))
			->join(array('dean'=>'tbl_deanlist'),'col.IdCollege = dean.IdCollege',array('dean.IdStaff','dean.FromDate','dean.ToDate'))
            ->where('col.IdCollege = ?',$IdCollege)
            ->where('dean.FromDate < now()');
	$result = $this->fetchAll($select);
	return $result->toArray();
    }

   public function fneditDeanDetails($IdCollege) { //Function for the view University
	$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('dean' => 'tbl_deanlist'),array('dean.*'))
            ->where('dean.IdCollege = ?',$IdCollege);
	$result = $this->fetchAll($select);
	return $result->toArray();
    }


    public function fnupdateCollege($formData,$lintIdCollege) { //Function for updating the university
    	$formData['Phone1'] = $formData['Phone1countrycode']."-".$formData['Phone1statecode']."-".$formData['Phone1'];
		unset($formData['Phone1countrycode']);
		unset($formData['Phone1statecode']);
		$formData['Phone2'] = $formData['Phone2countrycode']."-".$formData['Phone2statecode']."-".$formData['Phone2'];
		unset($formData['Phone2countrycode']);
		unset($formData['Phone2statecode']);

		$formData['Fax'] = $formData['Faxcountrycode']."-".$formData['Faxstatecode']."-".$formData['Fax'];
		unset($formData['Faxcountrycode']);
		unset($formData['Faxstatecode']);

    	unset ( $formData ['Save'] );
    	unset ( $formData ['IdStaff'] );
    	unset ( $formData ['FromDate'] );

		$where = 'IdCollege = '.$lintIdCollege;
		$this->update($formData,$where);
    }

	public function fnSearchCollege($post = array()) { //Function for searching the university details
		
		$field7 = "Active = ".$post["field7"];
		if($post['field5']=='All') { $post['field5'] = ''; }
		$select = $this->select()
		->setIntegrityCheck(false)
		->join(array('a' => 'tbl_collegemaster'),array('IdCollege'));
		if(isset($post['field3']) && !empty($post['field3'])){
			$select = $select->where('a.CollegeName  like "%" ? "%"',$post['field3']);
		}
		if(isset($post['field2']) && !empty($post['field2'])){
			$select = $select->where('a.ShortName like  "%" ? "%"',$post['field2']);
		}
		if(isset($post['field18']) && !empty($post['field18'])){
			$select = $select->where('a.ArabicName like  "%" ? "%"',$post['field18']);
		}
		if(isset($post['field4']) && !empty($post['field4'])){
			$select = $select->where('a.Email like  "%" ? "%"',$post['field4']);
		}
		 


		if(isset($post['field5']) && !empty($post['field5'])){
			$select = $select->where("a.AffiliatedTo = ?",$post['field5']);
		}
		$select ->where($field7)
		->order("a.CollegeName");
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function fnSearchUserCollege($post = array(),$IdCollege) { //Function for searching the university details
		$field7 = "Active = ".$post["field7"];
		$select = $this->select()
			   ->setIntegrityCheck(false)
			   ->join(array('a' => 'tbl_collegemaster'),array('IdCollege'))
			   ->where('a.CollegeName  like "%" ? "%"',$post['field3'])
			   ->where('a.ShortName like  "%" ? "%"',$post['field2'])
			   ->where('a.Email like  "%" ? "%"',$post['field4']);
			   //echo $select;
			if(isset($post['field5']) && !empty($post['field5'])){
				$select = $select->where("a.AffiliatedTo = ?",$post['field5']);
			}
			 $select ->where('a.IdCollege = ?',$IdCollege)
			   ->where($field7)
			   ->order("a.CollegeName");
			   //echo $select;
		$result = $this->fetchAll($select);
		return $result->toArray();
	}
	public function fnGetListofCollege(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.CollegeName"))
		 				 ->where("a.CollegeType = 0")
		 				 ->where("a.Active = 1")
		 				 ->order("a.CollegeName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetCollegeList($idCollege=0){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("key"=>"a.IdCollege","value"=>"a.CollegeName"))
		 				 //->where("a.CollegeType = 1")
		 				 ->where("a.Active = 1")
		 				 ->order("a.CollegeName");
		if($idCollege!=0){
			$lstrSelect->where("a.idCollege = ?", $idCollege);
		}
		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnValidateCollegeName($CollegeName) {
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("c"=>"tbl_collegemaster"),array("c.*"))
		            	->where("c.CollegeName= ?",$CollegeName);
		return $result = $lobjDbAdpt->fetchRow($select);
    }

	public function fnValidateCollegeCode($CollegeCode) {
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
/*		$select 	= $lobjDbAdpt->select()
						->from(array("c"=>"tbl_collegemaster"),array("c.*"))
		            	->where("c.CollegeCode= ?",$CollegeCode);
		            	echo $select;
		return $result = $lobjDbAdpt->fetchRow($select);*/
    }

    /**
     * Function to get faculty listing by universityID(affiliateID)
     * @author Vipul
     */
    public function fngetCollegemasterDetailsByAffltId($idUniversity) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_collegemaster"),array("a.*"))
		 				 //->where("a.AffiliatedTo = ?",$idUniversity)
		 				  ->order("a.CollegeName")
		 				 ->where("a.Active = 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }

	public function fngetCollegemasterData($college_id) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		if($college_id!=null){
			$lstrSelect = $lobjDbAdpt->select()
			 				 ->from(array("a"=>"tbl_collegemaster"))
			 				 ->where("a.IdCollege = ".$college_id)
			 				 ->order("a.CollegeName");
					 				  
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		
			return $larrResult;
		
		}else{
			return null;
		}
     }
     
 	public function getFullInfoCollege($collegeid) { //Function to get the user details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("cm"=>"tbl_collegemaster"))
		 				 ->joinLeft(array('c'=>'tbl_city'),'c.idCity=cm.City',array('CityName'))
		 				  ->joinLeft(array('s'=>'tbl_state'),'s.idState=cm.State',array('StateName'))
		 				 ->where("cm.IdCollege = ?",$collegeid);
		 				 
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
     }
}
?>