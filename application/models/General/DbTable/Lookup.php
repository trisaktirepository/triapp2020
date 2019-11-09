<?php 
class App_Model_General_DbTable_Lookup extends Zend_Db_Table_Abstract
{
    protected $_child = 'g017_definationms';
    protected $_parent = 'g018_definationtypems';
	protected $_primary = "id";
	protected $_student = 'r015_student';
	
	public function getData($type=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					    ->from(array('a'=>$this->_parent),array('parentID'=>'a.idDefType'))				       
				        ->join(array('b'=>$this->_child),'b.idDefType = a.idDefType',array('code'=>'b.DefinitionCode','name'=>'b.DefinitionDesc','id'=>'b.idDefinition'))
				        ->where('a.idDefType  = ?',$type)
				        ->order('b.DefinitionDesc ASC');
				        
		$row = $db->fetchAll($select);
		
		return $row;
	}
	
	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name));
		
		return $select;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'description' => $data['description'],
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'description' => $data['description']
		);
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function checkStudent($icno){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		  $select = $db->select()
		             ->from(array('s' => $this->_student))	
		             ->join(array('d' => $this->_child),
						's.ARD_RELIGION=d.idDefinition',
						array('a_religion'=>'d.DefinitionDesc'))	
					 ->join(array('c' => $this->_child),
						's.ARD_RACE=c.idDefinition',
						array('c_race'=>'c.DefinitionDesc'))				
					 ->where("s.ARD_IC='".$icno."'");
	 
        $row = $db->fetchRow($select);
//	      echo $select;
		return $row;
	}
}
?>