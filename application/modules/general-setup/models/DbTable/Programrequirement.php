<?php
class GeneralSetup_Model_DbTable_Programrequirement extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_programrequirement';
    private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

    public function fnaddProgramrequirement($formData) {
    		 $count = count($formData['SubjectTypegrid']);
    		 for($i = 0;$i<$count;$i++) {
    		$data = array('IdLandscape' =>$formData['IdLandscape'],
    					  'IdProgram' => $formData ['IdProgram'],
    					  'SubjectType' => $formData ['SubjectTypegrid'][$i],
						  'CreditHours' =>  $formData ['CreditHoursgrid'][$i],
    					  'UpdDate'  =>	$formData ['UpdDate'],
    					  'UpdUser'	=> 	$formData ['UpdUser']);
			 $this->insert($data);
    		 }
			$lobjdb = Zend_Db_Table::getDefaultAdapter();
			return $lobjdb->lastInsertId();
	}

    public function fnaddProgramrequirementlevel($formData,$resultLandscape) { //Function for adding the University details to the table.
    		 $count = count($formData['SubjectTypegrid']);
    		 for($i = 0;$i<$count;$i++) {
    		$data = array('IdLandscape' =>$resultLandscape,
    					  'IdProgram' => $formData ['IdProgram'],
    					  'SubjectType' => $formData ['SubjectTypegrid'][$i],
						  'CreditHours' =>  $formData ['CreditHoursgrid'][$i],
    					  'UpdDate'  =>	$formData ['UpdDate'],
    					  'UpdUser'	=> 	$formData ['UpdUser']);
			 $this->insert($data);
    		 }
			$lobjdb = Zend_Db_Table::getDefaultAdapter();
			return $lobjdb->lastInsertId();
	}

	/**
	 * Functon to get programe requirement for landscape
	 * @author: Vipul
	 */

	public function getlandscapeprogramreqmnt($id_program,$lid) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_programrequirement"))
		 				 ->where("a.IdProgram = ?",$id_program)
		 				 ->where("a.IdLandscape = ?",$lid);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	/**
	 * Functon to INSERT programe requirement for landscape
	 * @author: Vipul
	 */
	public function fninsertLandscapeProgramReqd($formData) {
			$data = array('IdLandscape' =>$formData ['IdLandscape'],
    					  'IdProgram' => $formData ['IdProgram'],
    					  'SubjectType' => $formData ['SubjectType'],
						  'CreditHours' =>  $formData ['CreditHours'],
    					  'UpdDate'  =>	$formData ['UpdDate'],
    					  'UpdUser'	=> 	$formData ['UpdUser']);
			$this->insert($data);
			$lobjdb = Zend_Db_Table::getDefaultAdapter();
			return $lobjdb->lastInsertId();

	}
        
        /**
	 * Functon to get course type ID and Name requirement for landscape
	 * @author: Vipul
	 */

	public function getlandscapecoursetype($id_program,$lid) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_programrequirement"))
                                                 ->join(array("b"=>"tbl_definationms"), 'b.idDefinition=a.SubjectType', array('category'=>'b.DefinitionDesc','kategori'=>'b.BahasaIndonesia','b.DefinitionDesc'))
		 				 ->where("a.IdProgram = ?",$id_program)
		 				 ->where("a.IdLandscape = ?",$lid);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
        
        /**
	 * Functon to get course type ID and Name requirement for landscape
	 * @author: Vipul
	 */

	public function getCourseTypeDeleted($id_program,$lid) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_tempprogramrequirement"))
                                                 ->join(array("b"=>"tbl_definationms"), 'b.idDefinition=a.SubjectType', array('b.DefinitionDesc'))
		 				 ->where("a.IdProgram = ?",$id_program)
                                                 ->where("a.deleteFlag = ?",'1')
		 				 ->where("a.unicode = ?",$lid);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	
	public function getProgReqbyLandscape($id_program,$lid) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_programrequirement"))
                         ->join(array("b"=>"tbl_definationms"), 'b.idDefinition=a.SubjectType')
		 				 ->where("a.IdProgram = ?",$id_program)
		 				 ->where("a.IdLandscape = ?",$lid);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
	}
	
	public function addData($data) {
    	    $this->insert($data);    	
	}
	
	public function updateData($data,$id) {		
		
    	  $this->update($data,'IdProgramReq='.$id);    	
	}
	
	public function deleteData($id){
			
	  $this->delete('IdProgramReq =' . (int)$id);
	}
	
	public function getInfo($IdProgramReq) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_programrequirement"))
                         ->join(array("b"=>"tbl_definationms"), 'b.idDefinition=a.SubjectType')		 				 
		 				 ->where("IdProgramReq = ?",$IdProgramReq);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		
		return $larrResult;
	}
	
	
	public function getListLandscapeCourseType($id_program,$lid,$compulsory=null) {
		
		 $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_programrequirement"))
                         ->join(array("b"=>"tbl_definationms"), 'b.idDefinition=a.SubjectType', array('DefinitionDesc'))
		 				 ->where("a.IdProgram = ?",$id_program)
		 				 ->where("a.IdLandscape = ?",$lid);
		 				 
		if($compulsory!=null){
			$lstrSelect->where("a.Compulsory = ?",$compulsory);
		}
		//echo $lstrSelect;
		
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
	}
	public function getDefinitionID($compname){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$sql = $lobjDbAdpt->select()
				->from(array("d"=>"tbl_definationms"),array('idDefinition'))
				->where("d.DefinitionDesc = ?",$compname);
		$larrResult = $lobjDbAdpt->fetchRow($sql);
		
		return $larrResult;
	}
	
	public function getIdProgReq($coursetype,$idlandscape){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_programrequirement"))	 				 
		 				 ->where("SubjectType = ?",$coursetype)
		 				 ->where("IdLandscape = ?",$idlandscape);
		 				 
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);

		return $larrResult["IdProgramReq"];		
	}	

}
?>