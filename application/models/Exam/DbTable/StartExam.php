<?php

class App_Model_Exam_DbTable_StartExam extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'q014_startexam';
	protected $_primary = "id";
		
	public function add($data){
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
	}
	
	public function updateData($data,$id){
		
//		echo $data;
//		echo $id;
//		exit;
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	public function checkStart($idSche,$idCourse, $idCenter){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		$select   = $db->select()
    				->from($this->_name)
					->where('idCenter  = ?',$idCenter)
					->where('idCourse  = ?',$idCourse)					
					->where('idSchedule = ?', $idSche)
					->order('id desc');
					//->limit(0, 1);					
            
        $row   = $db->fetchRow($select);

       return $row;
	}
	
	public function getGradeInfo_old($program_id,$semester_id,$mark){
		$db = Zend_Db_Table::getDefaultAdapter();	
				        
		$select = $db->select(array('group_id'=>'g.id'))
					    ->from(array('g'=>'e011_grade_group'),array('group_id'=>'g.id'))				       
				        ->join(array('cg'=>'e006_custom_grade'),'g.id = cg.grade_group_id',array())
				        ->where('cg.program_id  = ?',$program_id)
				        ->where('cg.semester_id = ?',$semester_id);
				        
		$rowSet1   = $db->fetchRow($select);
		
		//by default follow university		
		if(!is_array($rowSet1)){
			$select=1;
		}
		
		$select2   = $db->select()
    				->from($this->_name)
					->where('min_mark <= ?',$mark)
					->where('max_mark  > ?',$mark)					
					->where('grade_group_id = (?)', $select);							
       
            
        $rowSet2   = $db->fetchRow($select2);

       return $rowSet2;
    	
    }
    
}

