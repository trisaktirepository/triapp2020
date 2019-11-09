<?php
class App_Model_Record_DbTable_SemesterProgram extends Zend_Db_Table_Abstract
{
    protected $_name = 'r002_semester_program';
    protected $_primary = 'id';
	
    protected $_referenceMap = array (
		
    );
    
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name)
					->where($this->_name.".".$this->_primary .' = '. $id);
					
				$row = $db->fetchRow($select);
		}else{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
								
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getDataBySemester($semesterID=0){
		
		$semesterID = (int)$semesterID;
		
		if($semesterID!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('sp'=>$this->_name))
					->where('sp.semester_id = '. $semesterID)
					->join(array('program'=>'r006_program'), 'program.id = sp.program_id',array('program_code'=>'program.code'))
					->join(array('market'=>'r004_market'), 'market.id = program.market_id',array('market'=>'market.name'))
					->join(array('faculty'=>'g005_faculty'), 'faculty.id = program.faculty_id',array('faculty'=>'faculty.name'))
					->join(array('masterprogram'=>'r005_program_main'), 'masterprogram.id = program.program_main_id', array('main_name'=>'masterprogram.name'));
													
				$row = $db->fetchAll($select);
		}else{
			throw new Exception("There is No Data");
		}
		
		if(!$row){
			return null;
		}else{
			return $row;	
		}
		
		
	}
	
	public function getProgramSemester($semesterID=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($semesterID!=0){
			
			$selected = $this->getDataBySemester($semesterID);
			
			
			$selData = "";
			if(isset($selected)){
				foreach ($selected as $program){
					if($selData!=""){
						$selData = $selData.",".$program['program_id'];
					}else{
						$selData = $program['program_id'];
					}
				}
				
								
				/*$select = $db->select()
					->from($this->_name)
					->where('program_id not in ('.$selData.')')
					->join('program', 'program.program_id = semester_program.programID')
					->join('market', 'program.program_market_id = market.market_id')
					->join('faculty', 'program.program_faculty_id = faculty.faculty_id')
					->join('masterprogram', 'masterprogram.masterProgramID = program.program_master_id');*/
					
				$select = $db->select()
					->from(array('p'=>'r006_program'))
					->where('p.id not in ('.$selData.')')
					->join(array('mp'=>'r005_program_main'), 'mp.id = p.program_main_id', array('main_name'=>'mp.name'));
				
				
			}else{

				$select = $db->select()
					->from(array('p'=>'r006_program'))
					->join(array('mp'=>'r005_program_main'), 'mp.id = p.program_main_id', array('main_name'=>'mp.name'));
			}
			
			
			$stmt = $db->query($select);
		
			$row = $stmt->fetchAll();
			
			if($row){
				return $row;	
			}else{
				return null;
			}
			
		}else{
			return null;
		}
		
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name);
								
		return $select;
	}
	
    public function addSemesterProgram($semesterID,$programID)
    {
        $data = array(
            'semester_id' => $semesterID,
            'program_id' => $programID
        );
        $this->insert($data);
    }
    
    public function deleteSemesterProgram($semesterID)
    {
        $this->delete('semester_id =' . (int)$semesterID);
    }
}