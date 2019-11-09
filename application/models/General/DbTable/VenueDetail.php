<?php 
class App_Model_General_DbTable_VenueDetail extends Zend_Db_Table_Abstract
{
    protected $_name = 'g012_venue_detail';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){

	        $select = $db->select()
	                ->from(array('vd'=>$this->_name))
	                ->where('vd.'.$this->_primary.' = ' .$id)
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"vd.type_id = vt.id",
								array('type_name'=>'vt.name'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('vd'=>$this->_name))
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"vd.type_id = vt.id",
								array('type_name'=>'vt.name'));
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
	        if(!$row){
	        	$row =  $row->toArray();
	        }
		}
		
		return $row;
	}
	
	public function getPaginateData($venueID = 0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($venueID!=0){
			$select = $db->select()
	                ->from(array('vd'=>$this->_name))
	                ->where('vd.venue_id = ?', $venueID)
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"vd.type_id = vt.id",
								array('type_name'=>'vt.name'));	
		}else{
			$select = $db->select()
	                ->from(array('vd'=>$this->_name))
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"vd.type_id = vt.id",
								array('type_name'=>'vt.name'));
		}
		
		
		return $select;
	}
	
	public function addData($data){
		$data = array(
			'name' => $data['name'],
			'type_id' => $data['type_id'],
			'capacity' => $data['capacity'],
			'venue_id' => $data['venue_id']
		);
			
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		$data = array(
			'name' => $data['name'],
			'type_id' => $data['type_id'],
			'capacity' => $data['capacity'],
			'venue_id' => $data['venue_id']
		);
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getCapacity($venue_id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$venue_id = (int)$venue_id;
		

	        $select = $db->select()
	                ->from(array('vd'=>$this->_name), array('SUM(capacity)'))
	                ->where('vd.venue_id = ?', $venue_id);
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		
		
		return $row;
	}
	
	public function getCenterData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;


	        $select = $db->select()
	                ->from(array('vd'=>$this->_name))
	                ->where('vd.venue_id = ?', $id)
					->joinLeft(array('vt'=>'g010_venue_Type'),
								"vd.type_id = vt.id",
								array('type_name'=>'vt.name'));
			                     
			$result = $db->fetchAll($select);  
	        
	        if(!$result){
	        	return null;	
	        }else{
	        	return $result;
	        }
	}
}
?>