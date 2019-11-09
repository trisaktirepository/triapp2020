<?
class authentication_header {
  private $username;
  private $password;
  public function __construct($username,$password) {
     $this->Name=$username;
     $this->Password=$password;
  }
}

class CELSas{
	private $EL_USERNAME="POC-NCEL-Admin";
	private $EL_PASSWD="1qaz3edc";
	private $EL_URL="http://ec2-23-20-207-240.compute-1.amazonaws.com:90/DataMigration.svc?wsdl";
	
	
/*	private $EL_USERNAME="NCELAPIEDC4FL29CVN9V8";
	private $EL_PASSWD="Cv78fg2h/";	
	private $EL_URL="webservice.wsdl";*/
	
	function CELSas(){
		$this->client = new SOAPClient($this->EL_URL, array('trace' => 1));
		
	}
	
	function genAuthHeader(){
		// generate new object
		return 0;
		$auth=new authentication_header($this->EL_USERNAME,$this->EL_PASSWD);
				// create authentication header values
		$authvalues=new SoapVar($auth,SOAP_ENC_OBJECT,'BasicAuth');
				// generate header
		$this->header=new SoapHeader("sas",
		                           "BasicAuth",
		                           $authvalues,
		                           false,
		                            "http://schemas.xmlsoap.org/soap/actor/next");
	}
	
	function callFunction($function="getServerConfiguration",$params=null){
		$this->genAuthHeader();
		try {	
			  $this->client->__setSoapHeaders($this->header);
			  $this->result = $this->client->$function($params);
				//echo "x";
			} catch (Exception $e) {
				//echo "y";
				$err = $e->detail->ErrorResponse->message;
				$this->errorcall=1;
		     	$this->errormsg = 'Error Caught. '. $err;
			} 	
	}
}
?>