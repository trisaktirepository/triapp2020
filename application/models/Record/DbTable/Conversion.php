<?php
class App_Model_Record_DbTable_Conversion extends Zend_Db_Table_Abstract {
    
	protected $_name = 'tbl_conversion';
	protected $_primary='IdConversion';
	
   
    public function addConversion($bind){
        $db = Zend_Db_Table::getDefaultAdapter();
        $id=$this->insert($bind);
        return $id;
    }
    
    public function deleteConversion($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$this->delete($this->_primary."=".$id);
    }
    
    
    public function updateConversion($bind, $id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $update = $this->update($bind, $this->_primary.'='.$id);
        return $update;
    }
    public function addConversionMain($bind){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$id=$db->insert('tbl_conversion_main',$bind);
    	return $id;
    }
    
    
    public function updateConversionMain($bind, $id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->update('tbl_conversion_main',$bind, 'IdConversionMain='.$id);
    	return $update;
    }
    
    public function getConversion($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    		->from(array('c'=>$this->_name))
    		->join(array('sm'=>'tbl_subjectmaster'),'c.IdSubject=sm.IdSubject',array('SubCode'=>'sm.ShortName','SubjectName'=>"sm.BahasaIndonesia",'sm.CreditHours'))
    		->join(array('smnew'=>'tbl_subjectmaster'),'c.IdSubjectNew=smnew.IdSubject',array('SubCodeNew'=>'smnew.ShortName','SubjectNameNew'=>"smnew.BahasaIndonesia",'CreditHoursNew'=>'smnew.CreditHours'))
    		->where('c.IdConversionMain=?',$id);
    	$row=$db->fetchAll($update);
    	return $row;
    }
    
    public function getConversionMain($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    	->from(array('p'=>'tbl_conversion_main'))
    	->join(array('pm'=>'tbl_program'),'p.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
    	->joinLeft(array('m'=>'tbl_programmajoring'), 'p.IdProgramMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
    	->joinLeft(array('b'=>'tbl_branchofficevenue'),'p.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
    	->join(array('l'=>'tbl_landscape'),'l.IdLandscape=p.IdLandscape',array("Landscape"=>'ProgramDescription'))
    	->join(array('ln'=>'tbl_landscape'),'ln.IdLandscape=p.IdLandscapeNew',array("LandscapeNew"=>'ProgramDescription'))
    	->where('IdConversionMain=?',$id);
    	$row=$db->fetchRow($update);
    	return $row;
    }
    
    public function getAllSetupMain($program,$majoring,$branch){
    	$session = new Zend_Session_Namespace('sis');
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$lstrSelect = $db->select()
    	->from(array("p"=>'tbl_conversion_main'))
    	->join(array('pm'=>'tbl_program'),'p.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
    	->joinLeft(array('m'=>'tbl_programmajoring'), 'p.IdProgramMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
    	->joinLeft(array('b'=>'tbl_branchofficevenue'),'p.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
    	->join(array('l'=>'tbl_landscape'),'l.IdLandscape=p.IdLandscape',array("Landscape"=>'ProgramDescription'))
    	->join(array('ln'=>'tbl_landscape'),'ln.IdLandscape=p.IdLandscapeNew',array("LandscapeNew"=>'ProgramDescription'))
    	->where('p.IdProgram=?',$program)
    	->where('p.IdBranch=?',$branch)
    	->where('p.IdProgramMajoring=?',$majoring);
    	$larrResult = $db->fetchAll($lstrSelect);
    	if (!$larrResult) {
    		$lstrSelect = $db->select()
    		->from(array("p"=>'tbl_conversion_main'))
    		->join(array('pm'=>'tbl_program'),'p.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
    		->joinLeft(array('m'=>'tbl_programmajoring'), 'p.IdProgramMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
    		->joinLeft(array('b'=>'tbl_branchofficevenue'),'p.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
    		->join(array('l'=>'tbl_landscape'),'l.IdLandscape=p.IdLandscape',array("Landscape"=>'ProgramDescription'))
    		->join(array('ln'=>'tbl_landscape'),'ln.IdLandscape=p.IdLandscapeNew',array("LandscapeNew"=>'ProgramDescription'))
    		->where('p.IdProgram=?',$program)
    		 
    		->where('p.IdProgramMajoring=?',$majoring);
    		$larrResult = $db->fetchAll($lstrSelect);
    		if (!$larrResult) {
    			$lstrSelect = $db->select()
    			->from(array("p"=>'tbl_conversion_main'))
    			->join(array('pm'=>'tbl_program'),'p.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
    			->joinLeft(array('m'=>'tbl_programmajoring'), 'p.IdProgramMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
    			->joinLeft(array('b'=>'tbl_branchofficevenue'),'p.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
    			->join(array('l'=>'tbl_landscape'),'l.IdLandscape=p.IdLandscape',array("Landscape"=>'ProgramDescription'))
    			->join(array('ln'=>'tbl_landscape'),'ln.IdLandscape=p.IdLandscapeNew',array("LandscapeNew"=>'ProgramDescription'))
    			->where('p.IdProgram=?',$program);
    			  
    			$larrResult = $db->fetchAll($lstrSelect);
    		
    		}
    	}
    	return $larrResult;
    }
    public function getAllStudent($idmain) {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$lstrSelect = $db->select()
    	->from(array("p"=>'tbl_conversion_result'))
    	->join(array('sp'=>'student_profile'),'p.IdStudentRegistation=sp.IdStudentRegistation')
    	->join(array('st'=>'tbl_studentregistration'),'st.idApplication=sp.appl_id')
    	->where('p.IdConversionMain=?',$idmain)
    	->group('p.IdStudentRegistration');
    	$row=$db->fetchAll($lstrSelect);
    }
    public function getAllConversion($landscape,$newlandscape){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    	->from(array('c'=>$this->_name))
    	->join(array('cm'=>'tbl_conversion_main'),'c.IdConversionMain=cm.IdConversionMain')
    	->where('cm.idlandscape=?',$landscape)
    	->where('cm.idlandscapenew=?',$newlandscape);
    	$row=$db->fetchAll($update);
    	return $row;
    }
    
    public function getIdMain($program,$branch,$major,$landscape,$newlandscape){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    	->from(array('cm'=>'tbl_conversion_main'))
    	->where('cm.IdProgram =?',$program)
    	->where('cm.IdBranch=?',$branch)
    	->where('cm.IdProgramMajoring=?',$major)
    	->where('cm.idlandscape=?',$landscape)
    	->where('cm.idlandscapenew=?',$newlandscape);
    	$row=$db->fetchRow($update);
    	return $row;
    }
    public function getConversionBySubject($idmain,$landscape,$newlandscape,$idsubject){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    	->from(array('c'=>$this->_name))
    	->join(array('cm'=>'tbl_conversion_main'),'c.IdConversionMain=cm.IdConversionMain')
    	->join(array('sm'=>'tbl_subjectmaster'),'c.IdSubject=sm.IdSubject',array('SubCode'=>'sm.ShortName','SubjectName'=>"sm.BahasaIndonesia",'sm.CreditHours'))
    	->join(array('smnew'=>'tbl_subjectmaster'),'c.IdSubjectNew=smnew.IdSubject',array('SubCodeNew'=>'smnew.ShortName','SubjectNameNew'=>"smnew.BahasaIndonesia",'CreditHoursNew'=>'smnew.CreditHours'))
    	->where('cm.idlandscape=?',$landscape)
    	->where('c.IdConversionMain=?',$idmain)
    	->where('cm.idlandscapenew=?',$newlandscape)
    	->where('c.IdSubject=?',$idsubject);
    	$row=$db->fetchAll($update);
    	return $row;
    }
    public function getSubjectSetByIdSet($id,$idset){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    
    	->from(array('c'=>'tbl_subject_set'),array())
    	//->join(array('sm'=>'tbl_subjectmaster'),'c.IdSubjectSet=sm.IdSubject',array('key'=>'sm.IdSubject', 'value'=>"sm.BahasaIndonesia" ))
    	->join(array('smnew'=>'tbl_subjectmaster'),'c.IdSubject=smnew.IdSubject',array('value'=>'CONCAT(smnew.ShortName,"-",smnew.BahasaIndonesia)','key'=>"smnew.IdSubject"))
    	->where('c.IdConversionMain=?',$id)
    	->where('c.IdSubjectSet=?',$idset);
    	$row=$db->fetchAll($update);
    	return $row;
    }
    
    public function getCountSubject($landscape,$newlandscape,$idsubjectnew){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    	->from(array('c'=>$this->_name))
    	->join(array('cm'=>'tbl_conversion_main'),'c.IdConversionMain=cm.IdConversionMain')
    	->where('cm.idlandscape=?',$landscape)
    	->where('cm.idlandscapenew=?',$newlandscape)
    	->where('c.IdSubjectNew=?',$idsubjectnew);
    	$row=$db->fetchAll($update);
    	return $row;
    }
    
    public function getConversionBySubjectNew($idmain,$landscape,$newlandscape,$idsubject){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$update = $db->select()
    	->from(array('c'=>$this->_name))
    	->join(array('cm'=>'tbl_conversion_main'),'c.IdConversionMain=cm.IdConversionMain')
    	->join(array('sm'=>'tbl_subjectmaster'),'c.IdSubject=sm.IdSubject',array('SubCode'=>'sm.ShortName','SubjectName'=>"sm.BahasaIndonesia",'sm.CreditHours'))
    	->join(array('smnew'=>'tbl_subjectmaster'),'c.IdSubjectNew=smnew.IdSubject',array('SubCodeNew'=>'smnew.ShortName','SubjectNameNew'=>"smnew.BahasaIndonesia",'CreditHoursNew'=>'smnew.CreditHours'))
    	->where('cm.idlandscape=?',$landscape)
    	->where('c.IdConversionMain=?',$idmain)
    	->where('cm.idlandscapenew=?',$newlandscape)
    	->where('c.IdSubjectNew=?',$idsubject);
    	$row=$db->fetchAll($update);
    	return $row;
    }
    
}