<?php
class Studentfinance_Model_DbTable_DiscountMain extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_discount_main';
	protected $_primary='id_dm';
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
	 
public function getCurrentSetup($univ,$kkni,$college,$program,$branch,$semester,$idmajoring,$iddiscount) {
		
		$selectsmt=$this->lobjDbAdpt->select()
		->from('tbl_semestermaster')
		->where('idsemestermaster=?',$semester);
		$sem=$this->lobjDbAdpt->fetchRow($selectsmt);
		$datestart=$sem['SemesterMainStartDate']; 
		
		
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
		->where('idDiscount=?',$iddiscount) 
		->where('iduniv=?',$univ)
		->where('idcollege=?',$college)
		->where('kkni=?',$kkni)
		->where('idprogram=?',$program)
		->where('idbranch=?',$branch)
		->where('a.majoring=?',$idmajoring)
		->where('b.SemesterMainStartDate <=?',$datestart)
		->order('b.SemesterMainStartDate DESC');
		//echo $select;exit;
		$row=$this->lobjDbAdpt->fetchRow($select);
		if (!$row) {
			$select=$this->lobjDbAdpt->select()
			->from(array('a'=>$this->_name))
			->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
			->where('idDiscount=?',$iddiscount) 
			->where('iduniv=?',$univ)
			->where('idcollege=?',$college)
			->where('kkni=?',$kkni)
			->where('idprogram=?',$program)
			->where('a.majoring=?',$idmajoring)
			->where('idbranch is null or idbranch=0')
			->where('b.SemesterMainStartDate <=?',$datestart)
			->order('b.SemesterMainStartDate DESC');
			$row=$this->lobjDbAdpt->fetchRow($select);
			//echo var_dump($row);echo $select;exit;
			if (!$row) {
				$select=$this->lobjDbAdpt->select()
				->from(array('a'=>$this->_name))
				->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
				->where('idDiscount=?',$iddiscount) 
				->where('iduniv=?',$univ)
				->where('idcollege=?',$college)
				->where('kkni=?',$kkni)
				->where('idprogram=?',$program)
				->where('idbranch=?',$branch)
				->where('majoring is null or majoring=0')
				->where('b.SemesterMainStartDate <=?',$datestart)
				->order('b.SemesterMainStartDate DESC');
				$row=$this->lobjDbAdpt->fetchRow($select);
				if (!$row) {
					 
						$select=$this->lobjDbAdpt->select()
						->from(array('a'=>$this->_name))
						->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
						->where('idDiscount=?',$iddiscount)
						 ->where('iduniv=?',$univ)
						->where('idcollege=?',$college)
						->where('kkni=?',$kkni)
						->where('idprogram=?',$program)
						->where('idbranch is null or idbranch=0')
						->where('majoring is null or majoring=0')
						->where('b.SemesterMainStartDate <=?',$datestart)
						->order('b.SemesterMainStartDate DESC');
						$row=$this->lobjDbAdpt->fetchRow($select);
					if (!$row) {
					$select=$this->lobjDbAdpt->select()
					->from(array('a'=>$this->_name))
					->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
					->where('idDiscount=?',$iddiscount)
					 ->where('iduniv=?',$univ)
					 ->where('kkni=?',$kkni)
					->where('idcollege=?',$college)
					
					->where('idprogram is null or idprogram=0')
					->where('idbranch is null or idbranch=0')
					->where('majoring is null or majoring=0')
					->where('b.SemesterMainStartDate <=?',$datestart)
					->order('b.SemesterMainStartDate DESC');
					$row=$this->lobjDbAdpt->fetchRow($select);
					
					if (!$row) {
						$select=$this->lobjDbAdpt->select()
						->from(array('a'=>$this->_name))
						->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
						->where('idDiscount=?',$iddiscount)
						 ->where('iduniv=?',$univ)
						 ->where('kkni=?',$kkni)
						->where('idcollege is null or idcollege=0')
						->where('idprogram is null or idprogram=0')
						->where('idbranch is null or idbranch=0')
						->where('majoring is null or majoring=0')
						->where('b.SemesterMainStartDate <=?',$datestart)
						->order('b.SemesterMainStartDate DESC');
						$row=$this->lobjDbAdpt->fetchRow($select);
						if (!$row) {
							$select=$this->lobjDbAdpt->select()
							->from(array('a'=>$this->_name))
							->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
							->where('idDiscount=?',$iddiscount)
							->where('iduniv=?',$univ)
							->where('kkni="" or kkni is null')
							->where('idcollege is null or idcollege=0')
							->where('idprogram is null or idprogram=0')
							->where('idbranch is null or idbranch=0')
							->where('majoring is null or majoring=0')
							->where('b.SemesterMainStartDate <=?',$datestart)
							->order('b.SemesterMainStartDate DESC');
							$row=$this->lobjDbAdpt->fetchRow($select);
								
						}
					}
				  }
				}
			}
		}
		//if ($row) echo $select;
		return $row;
	}
	
	public function getData($univ) {
	
		 
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('u'=>"tbl_universitymaster"),'a.iduniv=u.IdUniversity')
		->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
		->join(array('ds'=>'discount_type'),'ds.dt_id=a.idDiscount')
		->joinleft(array('c'=>'tbl_collegemaster'),'c.idcollege=a.idcollege')
		->joinleft(array('p'=>'tbl_program'),'p.IdProgram=a.idprogram')
		->joinleft(array('mj'=>'tbl_programmajoring'),'mj.IDProgramMajoring=a.majoring',array('Majoring'=>'BahasaDescription'))
		->joinleft(array('d'=>'tbl_branchofficevenue'),'d.IdBranch=a.idbranch')
		->where('iduniv=?',$univ);
		$row=$this->lobjDbAdpt->fetchAll($select);
		return $row;
	}
	
	
	public function isStudentApplied($iddm,$idstd=null) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_discount_student'))
		->where('id_dm=?',$iddm);
		if ($idstd!=null)
			$select->where('idstudentregistration=?',$idstd);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
	
	public function isSemesterApplied($iddm,$semester=null) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_discount_semester'))
		->where('id_dm=?',$iddm);
		if ($semester!=null)
			$select->where('idsemestermain=?',$semester);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
	
	public function getDiscount($iddm,$feeitem=null) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_discount_fee_item'))
		->join(array('b'=>'fee_item'),'b.fi_id=a.fee_id')
		->where('id_dm=?',$iddm);
		if ($feeitem!=null) {
			$select->where('a.fee_id=?',$feeitem);
			$row=$this->lobjDbAdpt->fetchRow($select);
		} else 
			$row=$this->lobjDbAdpt->fetchAll($select);
		//echo $select;
		return $row;
	}
	
	public function isLevelApplied($iddm,$level=null) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_discount_level'))
		->where('id_dm=?',$iddm);
		if ($level!=null)
			$select->where('level=?',$level);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
	
	public function isIntakeApplied($iddm,$intake=null) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_discount_intake'))
		->where('id_dm=?',$iddm);
		if ($intake!=null)
			$select->where('idintake=?',$intake);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
	
	public function getDiscountType() {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>'discount_type'));
		 
		$row=$this->lobjDbAdpt->fetchAll($select);
		return $row;
	}
	
	public function getDataById($id) {
	
			
		$select=$this->lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('u'=>"tbl_universitymaster"),'a.iduniv=u.IdUniversity')
		->join(array('b'=>'tbl_semestermaster'),'a.idsemestermain=b.idsemestermaster')
		->join(array('ds'=>'discount_type'),'ds.dt_id=a.idDiscount')
		->joinleft(array('c'=>'tbl_collegemaster'),'c.idcollege=a.idcollege')
		->joinleft(array('p'=>'tbl_program'),'p.IdProgram=a.idprogram')
		->joinleft(array('mj'=>'tbl_programmajoring'),'mj.IDProgramMajoring=a.majoring',array('Majoring'=>'BahasaDescription'))
		->joinleft(array('d'=>'tbl_branchofficevenue'),'d.IdBranch=a.idbranch')
		->where('id_dm=?',$id);
		$row=$this->lobjDbAdpt->fetchRow($select);
		return $row;
	}
}