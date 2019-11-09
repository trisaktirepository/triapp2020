<?php
class icampus_Helper_Acl extends Zend_ACL {
	
	private $_db;
	private $_role;
	
	public function __construct($role=0){
		//$this->acl = new Zend_Acl();
		$this->_role = $role;
		
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		echo $config->db->params->dbname;
		$params = array('host'=>'127.0.0.1',
						'username' => 'root',
						'password'=>'',
						'dbname'=>'icampus'
					);
		$this->_db= Zend_Db::factory('Pdo_Mysql', $params);
	}
	
	public function setRoles(){
		
		/*$this->addRole(new Zend_Acl_Role('1'));
		$this->addRole(new Zend_Acl_Role('editor'));
		$this->addRole(new Zend_Acl_Role('admin'));*/
		
		$select = $this->_db->select()
	                 ->from(array('r'=>'sys003_role'));
	        
        $stmt = $this->_db->query($select);
        $roles = $stmt->fetchAll();
        		
		foreach ($roles as $role) {
			$this->addRole(new Zend_Acl_Role($role['id']));
		}
	}

	public function setResources(){

		/*$this->add(new Zend_Acl_Resource('view'));
		$this->add(new Zend_Acl_Resource('add'));
		$this->add(new Zend_Acl_Resource('edit'));
		$this->add(new Zend_Acl_Resource('delete'));*/
		
		$select = $this->_db->select()
	                 ->from(array('c'=>'sys005_controller'))
	                 ->join(array('m'=>'sys002_module'),'c.module_id = m.id',array('module'=>'name'));
	        
        $stmt = $this->_db->query($select);
        $roles = $stmt->fetchAll();
        		
		foreach ($roles as $role) {
			$this->add(new Zend_Acl_Resource(strtolower($role['module'])."_".strtolower($role['name'])));	
		}
		
		/*default page*/
		$this->add(new Zend_Acl_Resource('default_index'));
		$this->add(new Zend_Acl_Resource('default_error'));
	}

	public function setPrivilages(){
		/*$this->allow('guest',null,'view');
		$this->allow('editor',array('view','edit'));
		$this->allow('admin');*/
		
		$select = $this->_db->select()
	                 ->from(array('p'=>'sys004_privilege'))
	                 ->where('p.role_id =' .$this->_role)
	                 ->join(array('m'=>'sys002_module'),'p.module_id = m.id',array('module'=>'name'))
	                 ->join(array('c'=>'sys005_controller'),'p.controller_id = c.id',array('controller'=>'name'));
	        
        $stmt = $this->_db->query($select);
        $privileges = $stmt->fetchAll();
        
        foreach ($privileges as $privilege) {
        	if($privilege['view']==1){
        		$this->allow($privilege['role_id'],strtolower($privilege['module']."_".$privilege['controller']),"view");
        	}
        	
        	if($privilege['add']==1){
        		$this->allow($privilege['role_id'],strtolower($privilege['module']."_".$privilege['controller']),"add");
        	}
        	
        	if($privilege['edit']==1){
        		$this->allow($privilege['role_id'],strtolower($privilege['module']."_".$privilege['controller']),"edit");
        	}
        	
        	if($privilege['delete']==1){
        		$this->allow($privilege['role_id'],strtolower($privilege['module']."_".$privilege['controller']),"delete");
        	}
        	
        }
        
        /*Default page*/
        $this->allow($this->_role,"default_index",array('view','add', 'edit','delete'));
        
	}
	
	public function setAcl(){
		Zend_Registry::set('acl',$this);
	}
}
?>