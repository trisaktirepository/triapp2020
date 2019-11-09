<?php
 
  ini_set("soap.wsdl_cache_enabled", "0");
  $APIKey = "1234ABC";
   
  $soap = new SoapClient('http://lms.oum.edu.my/myvle/webservicecylonlive/service.wsdl');
  try {
 
          $data = $soap->getUserInfo("student", $APIKey);
      echo "<pre>";
      print_r($data);
      echo "</pre>";
   
  } catch (SoapFault $e) {
          echo "Error: {$e->faultstring}";
  }
?>