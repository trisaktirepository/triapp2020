<?php
class Studentfinance_Model_DbTable_BundleFee extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'fee_budle';
	protected $_primary='idfeebundle';
	protected $lobjDbAdpt;
	
	public function init(){
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($data) {
		return $this->lobjDbAdpt->insert($this->_name,$data);
	}
	
	public function updatData($data,$where){
		return $this->lobjDbAdpt->update($this->_name,$data,$where);
	}
	
	public function deleteData($where){
		return $this->lobjDbAdpt->delete($this->_name,$where);
	}
	 
	public function getCurrentSetup($univ,$college,$program,$branch,$semester,$idactivity,$idmajoring) {
		
		$selectsmt=$this->lobjDbAdpt->select()
		->from('tbl_semestermaster')
		->where('idsemestermaster=?',$semester);
		$sem=$this->lobjDbAdpt->fetchRow($selectsmt);
		$datestart=$sem['SemesterMainStartDate'];
		$semesterFunctionType=$sem['SemesterFunctionType'];
		$semesterCountTtype=$sem['SemesterCountType'];
		
		
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
		->where('idactivity=?',$idactivity)
		->where('b.SemesterCountType=?',$semesterCountTtype)
		->where('b.SemesterFunctionType=?',$semesterFunctionType)
		->where('iduniv=?',$univ)
		->where('idcollege=?',$college)
		->where('idprogram=?',$program)
		->where('idbranch=?',$branch)
		->where('a.majoring=?',$idmajoring)
		//->where('b.SemesterMainStartDate <=?',$datestart)
		->order('b.SemesterMainStartDate DESC');
		
		$row=$this->lobjDbAdpt->fetchRow($select);
		if (!$row) {
			$select=$this->lobjDbAdpt->select()
			->from(array('a'=>$this->_name))
			->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
			->where('idactivity=?',$idactivity)
			->where('b.SemesterCountType=?',$semesterCountTtype)
			->where('b.SemesterFunctionType=?',$semesterFunctionType)
			->where('iduniv=?',$univ)
			->where('idcollege=?',$college)
			->where('idprogram=?',$program)
			->where('a.majoring=?',$idmajoring)
			->where('idbranch is null or idbranch=0')
			//->where('b.SemesterMainStartDate <=?',$datestart)
			->order('b.SemesterMainStartDate DESC');
			$row=$this->lobjDbAdpt->fetchRow($select);
			//echo var_dump($row);echo $select;exit;
			if (!$row) {
				$select=$this->lobjDbAdpt->select()
				->from(array('a'=>$this->_name))
				->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
				->where('idactivity=?',$idactivity)
				->where('b.SemesterCountType=?',$semesterCountTtype)
				->where('b.SemesterFunctionType=?',$semesterFunctionType)
				->where('iduniv=?',$univ)
				->where('idcollege=?',$college)
				->where('idprogram=?',$program)
				->where('idbranch is null or idbranch=0')
				->where('majoring is null or majoring=0')
				//->where('b.SemesterMainStartDate <=?',$datestart)
				->order('b.SemesterMainStartDate DESC');
				$row=$this->lobjDbAdpt->fetchRow($select);
				if (!$row) {
					$select=$this->lobjDbAdpt->select()
					->from(array('a'=>$this->_name))
					->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
					->where('idactivity=?',$idactivity)
					->where('b.SemesterCountType=?',$semesterCountTtype)
					->where('b.SemesterFunctionType=?',$semesterFunctionType)
					->where('iduniv=?',$univ)
					->where('idcollege=?',$college)
					->where('idprogram is null or idprogram=0')
					->where('idbranch is null or idbranch=0')
					->where('majoring is null or majoring=0')
					//->where('b.SemesterMainStartDate <=?',$datestart)
					->order('b.SemesterMainStartDate DESC');
					$row=$this->lobjDbAdpt->fetchRow($select);
					
					if (!$row) {
						$select=$this->lobjDbAdpt->select()
						->from(array('a'=>$this->_name))
						->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
						->where('idactivity=?',$idactivity)
						->where('b.SemesterCountType=?',$semesterCountTtype)
						->where('b.SemesterFunctionType=?',$semesterFunctionType)
						->where('iduniv=?',$univ)
						->where('idcollege is null or idcollege=0')
						->where('idprogram is null or idprogram=0')
						->where('idbranch is null or idbranch=0')
						->where('majoring is null or majoring=0')
						//->where('b.SemesterMainStartDate <=?',$datestart)
						->order('b.SemesterMainStartDate DESC');
						$row=$this->lobjDbAdpt->fetchRow($select);
						 
					}
				}
			}
		}
		//echo $select;
		return $row;
	}
	
	
	public function getData($univ) {
	
		 
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('u'=>"tbl_universitymaster"),'a.iduniv=u.IdUniversity')
		->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
		->joinleft(array('c'=>'tbl_collegemaster'),'c.idcollege=a.idcollege')
		->joinleft(array('p'=>'tbl_program'),'p.IdProgram=a.idprogram')
		->joinleft(array('mj'=>'tbl_programmajoring'),'mj.IDProgramMajoring=a.majoring',array('Majoring'=>'BahasaDescription'))
		->joinleft(array('d'=>'tbl_branchofficevenue'),'d.IdBranch=a.idbranch')
		->where('iduniv=?',$univ);
		$row=$this->lobjDbAdpt->fetchAll($select);
		return $row;
	}
	
	public function getDataById($id) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('u'=>"tbl_universitymaster"),'a.iduniv=u.IdUniversity')
		->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
		->joinleft(array('c'=>'tbl_collegemaster'),'c.idcollege=a.idcollege')
		->joinleft(array('p'=>'tbl_program'),'p.IdProgram=a.idprogram')
		->joinleft(array('mj'=>'tbl_programmajoring'),'mj.IDProgramMajoring=a.majoring',array('Majoring'=>'BahasaDescription'))
		
		->joinleft(array('d'=>'tbl_branchofficevenue'),'d.IdBranch=a.idbranch')
		->where('idfeebundle=?',$id);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
}