<?php
//require($_SERVER["DOCUMENT_ROOT"].'/epic/public/index.php');

//$config =  Zend_Registry::get('config');
//print_r($config);
//echo $config->resources->db->params->host;

/*$params = array('host'=>$config->resources->db->params->host,
'username' => $config->resources->db->params->username,
'password'=>$config->resources->db->params->password,
'dbname'=>$config->resources->db->params->dbname
);
$db= Zend_Db::factory($config->resources->db->adapter, $params);*/

$kfm_db_type = 'mysql';
$kfm_db_prefix   = 'kfm_';
$kfm_db_host     = 'localhost';
$kfm_db_name     = 'trisakti_app';
$kfm_db_username = 'root';
$kfm_db_password = 'aucms4dm1n';
$kfm_db_port     = '';
$use_kfm_security=false;

$kfm_userfiles_address = '/var/www/html/triapp/resources';

// where should a browser look to find the files?
// Note that this is usually the same as $kfm_userfiles_address (if it is relative), but could be different
//   in the case that the server uses mod_rewrite or personal web-sites, etc
// Use the value 'get.php' if you want to use the KFM file handler script to manage file downloads.
// If you are not using get.php, this value must end in '/'.
// Examples:
//   $kfm_userfiles_output = 'http://thisdomain.com/files/';
//   $kfm_userfiles_output = '/files/';
//   $kfm_userfiles_output = 'http://thisdomain.com/kfm/get.php';
//   $kfm_userfiles_output = '/kfm/get.php';
$kfm_userfiles_output = '/resources';


//$kfm_userfiles_address = '/opt/lampp/htdocs/epic/upload/photo';
//$kfm_userfiles_output = '/epic/public/kfm/get.php';
$kfm_workdirectory = '.files';
#$kfm_workdirectory = '.files-sqlite-pdo';
$kfm_imagemagick_path = '/opt/lampp/bin/convert';
#$kfm_imagemagick_path = '/usr/bin/cnvert';
$kfm_dont_send_metrics = 0;
$kfm_server_hours_offset = 1;
