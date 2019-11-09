<?php
class App_Model_General_DbTable_Landscape extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_landscape';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
    
	public function getActiveLandscape($IdProgram,$idIntake) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter(); 

		$select = $lobjDbAdpt->select()	
						->from(array('lan' => 'tbl_landscape'))			
                        ->where('lan.IdProgram = ?',(int)$IdProgram)
						->where('lan.IdStartSemester = ?',(int)$idIntake)
						->where('lan.Active = ?',1)
						->order("lan.Default");
		
		$larrResult = $lobjDbAdpt->fetchAll($select);
		
        if(count($larrResult) < 1)
        {
            
            $sql = $lobjDbAdpt->select()	
						->from(array('lan' => 'tbl_landscape'))	
                        ->join(array('in' => 'tbl_intake'),'lan.IdStartSemester = in.IdIntake')
						->where('lan.IdProgram = ?',(int)$IdProgram)
						//->where('lan.IdStartSemester = ?',(int)$idIntake)
						->where('lan.Active = ?',1)
						->order("in.ApplicationStartDate DESC");
            //echo $sql;
            $larrResult = $lobjDbAdpt->fetchAll($sql);
        }
        return $larrResult;
	}
	
    
    public function getProgramId()
    {
        $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
        
        $select = $lobjDbAdpt->select()	
						->from(array('lan' => 'tbl_landscape'),array('lan.IdProgram'))			
						->where('lan.Active = ?',1)
						->order("lan.Default")
                        ->group('lan.IdProgram');
    
        $larrResult = $lobjDbAdpt->fetchAll($select);
        //print_r($larrResult);
        $idProgramString = '';
        foreach ($larrResult as $key => $value)
        {
            $idProgramString .= $value['IdProgram'].", ";
        }
		
        $idProgramString = rtrim($idProgramString,', ');
        return $idProgramString;
    }

    public function getLandscapeSubjects($IdProgram,$IdLandscape) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter(); 
        $lstrSelect = $lobjDbAdpt->select()
		->from(array("c" =>"tbl_landscapesubject"),array())
		->joinLeft(array("d"=>"tbl_subjectmaster"),"c.IdSubject = d.IdSubject AND d.Active=1 ",
            array("d.CreditHours","key" => "d.IdSubject",
            "value" => "CONCAT_WS(' - ',IFNULL(d.BahasaIndonesia,''),IFNULL(d.SubCode,''))"))
        ->where("c.IdProgram =?",$IdProgram)
        ->where("c.IdLandscape=?",$IdLandscape)
		->group("d.IdSubject")
		->order("d.BahasaIndonesia");
		//echo $lstrSelect; //die;
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
    
    public function getLandscapeSubjectBlock($IdProgram,$IdLandscape)
    {
        $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter(); 
        $lstrSelect = $lobjDbAdpt->select()
		->from(array("c" =>"tbl_landscapeblocksubject"),array())
		->joinLeft(array("d"=>"tbl_subjectmaster"),"c.subjectid = d.IdSubject AND d.Active=1 ",
            array("key" => "d.IdSubject",
            "value" => "CONCAT_WS(' - ',IFNULL(d.BahasaIndonesia,''),IFNULL(d.SubCode,''))"))
        //->where("c.IdProgramReq =?",$IdProgram)
        ->where("c.IdLandscape=?",$IdLandscape)
		->group("d.IdSubject")
		->order("d.BahasaIndonesia");
		//echo $lstrSelect; //die;
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
    }
    
    public function getLandscape($IdLandscape) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter(); 

		$select = $lobjDbAdpt->select()	
						->from(array('lan' => 'tbl_landscape'))			
                        ->where('lan.IdLandscape = ?',(int)$IdLandscape)
						->order("lan.Default");
		
		$larrResult = $lobjDbAdpt->fetchRow($select);
		
       
        return $larrResult;
	}
	
	public function getData($idlandscape){
     	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array('l'=>$this->_name))
		 				 ->where("l.IdLandscape = ?",$idlandscape);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
     }
	
	
}
?>
