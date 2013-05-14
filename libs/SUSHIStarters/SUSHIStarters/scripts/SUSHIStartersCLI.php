<?php

// Check the arguments received
if ( realpath($_SERVER['argv'][0]) == __FILE__ ) {
  if ( $argc == 4 )
  {
    $argSP=$argv[1];
    $argRpt=$argv[2];
    $argDateRange=$argv[3];
  }
  else
  {
    fwrite(STDOUT,"usage:\n");
    fwrite(STDOUT,"  php SUSHIStartersCLI.php \"Service Provider\" reporttype daterange\n");
    fwrite(STDOUT,"       \"Service Provider\": Name of a Service Provider exactly as you configured it\n");
    fwrite(STDOUT,"                             surrounded by quotes\n");
    fwrite(STDOUT,"       reporttype: Type of report you want, e.g. JR1, JR1a, BR3\n");
    fwrite(STDOUT,"       daterange: either YYYY-MM:YYYY-MM or lastmonth\n");
    exit;
  }
}
else
{
  if ( $argc == 3 )
  {
    $argSP=$argv[0];
    $argRpt=$argv[1];
    $argDateRange=$argv[2];
  }
  else
  {
    fwrite(STDOUT,"usage:\n");
    fwrite(STDOUT,"  SUSHIStartersCLI.php \"Service Provider\" reporttype daterange\n");
    fwrite(STDOUT,"       \"Service Provider\": Name of a Service Provider exactly as you configured it\n");
    fwrite(STDOUT,"                             surrounded by quotes\n");
    fwrite(STDOUT,"       reporttype: Type of report you want, e.g. JR1, JR1a, BR3\n");
    fwrite(STDOUT,"       daterange: either YYYY-MM:YYYY-MM or lastmonth\n");
    exit;
  }
}

include_once '../SSIncludes/helpers.inc.php';

$Extension = '';
$error = '';
$msg = '';
$Login = '';
$Password = '';

$ServiceProvider = str_replace('"','',$argSP);
$Name = $argRpt;
if (strtolower($argDateRange) == 'lastmonth')
{
  // convert 'lastmonth' to 'YYYY-MM' Begin and End Dates
  $Begin = date("Y-m", mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
  $End = $Begin;
}
else
{
  // split daterange into 'YYYY-MM' Begin and End Dates
  list($Begin, $End) = split(':', $argDateRange);
}
$Begin .= '-01';
$End .= '-'.date('t',strtotime($End.'-01'));
$Created = date("Y-m-d\TH:i:s.0\Z");
$ID = uniqid("SUSHIStarters:", true);

// get the params for the service provider
if (($handle = fopen($SSServicesCSV, "r")) !== FALSE)
{
  while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
  {
    $Provider = $data[1];
    if ($ServiceProvider == $data[1])
    {
      $ServiceURL = $data[3];
      $WSDLURL = $data[4];
      $RequestorID = $data[5];
      $RequestorName = $data[6];
      $RequestorEmail = $data[7];
      $CustomerReferenceID = $data[8];
      $CustomerReferenceName = $data[9];
      $Security = $data[10];
      $Login = $data[11];
      $Password = $data[12];
      break;
    }
  }
  fclose($handle);
}

if (preg_match("/^BR/", $Name))
{
  $Release = 1;
}
else
{
  $Release = 3;
}

if (strtoupper($WSDLURL) != 'COUNTER')
{
  $WSDL = $WSDLURL;
}

// look at $Security to ses if it uses an extension
if(preg_match('/Extension=/i', $Security))
{
  $Extensions = array();
  $varlist = explode(";", $Security);
  foreach( $varlist as $params)
  {
    list($extVar, $extVal) = explode("=", $params);
    $Extensions[$extVar] = $extVal;
    if ($extVar == 'Extension')
    {
      $Extension = $extVal;
    }
  }
}

if (!empty($Extension))
{
  include 'SSIncludes/extension_'.$Extension.'.inc.php';
}
else
{
  if (preg_match("/http/i", $Security))
  {
    try
    {
      $client = new SoapClient($WSDL,array
        (
          'login'          => $Login,
          'password'       => $Password,
          'location' => $ServiceURL,
          "trace"      => 1,
          "exceptions" => 1,
        )
      );
    }
    catch (Exception $e)
    {
      $error = $e->__toString();
      fwrite(STDERR,"Failed to connect to $ServiceProvider: $error\n");
      exit();
    }
  }
  else
  {
    if (strtoupper($WSDLURL) != 'COUNTER')
    {
      try
      {
        $client = new SoapClient($WSDL,array(
        "trace"      => 1,
        "exceptions" => 1));
      }
      catch (Exception $e)
      {
        $error = $e->__toString();
        fwrite(STDERR,"Failed to connect to $ServiceProvider: $error\n");
        exit();
      }
    } else {
      try
      {
        $client = new SoapClient($WSDL,array(
        'location' => $ServiceURL,
        "trace"      => 1,
        "exceptions" => 1));
      }
      catch (Exception $e)
      {
        $error = $e->__toString();
        fwrite(STDERR,"Failed to connect to $ServiceProvider: $error\n");
        exit();
      }
    }
  }

  if (preg_match("/wsse/i", $Security))
  {
    // Prepare SoapHeader parameters
    class clsWSSEAuth
    {
      private $Username;
      private $Password;
      function __construct($username, $password)
      {
        $this->Username=$username;
        $this->Password=$password;
      }
    }
    class clsWSSEToken
    {
      private $UsernameToken;
      function __construct ($innerVal)
      {
        $this->UsernameToken = $innerVal;
      }
    }

    $strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
    $objSoapVarUser = new SoapVar($Login, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
    $objSoapVarPass = new SoapVar($Password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
    $objWSSEAuth = new clsWSSEAuth($objSoapVarUser, $objSoapVarPass);
    $objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
    $objWSSEToken = new clsWSSEToken($objSoapVarWSSEAuth);
    $objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
    $objSoapVarHeaderVal=new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);
    $objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal,false);

    // Prepare Soap Client
    try
    {
      $client->__setSoapHeaders(array($objSoapVarWSSEHeader));
    }
    catch (Exception $e)
    {
      $error = $e->getMessage();
      fwrite(STDERR,"Failed to set SoapHeaders for $ServiceProvider: $error\n");
    }

  }

  try
  {
    $result = $client->GetReport(array
                     (
                       'Requestor' => array
                       (
                         'ID' => $RequestorID,
                         'Name' => $RequestorName,
                         'Email' => $RequestorEmail
                       ),
                       'CustomerReference' => array
                       (
                         'ID' => $CustomerReferenceID,
                         'Name' => $CustomerReferenceName
                       ),
                       'ReportDefinition'  => array
                       (
                         'Filters' => array
                         (
                           'UsageDateRange' => array
                           (
                             'Begin' => $Begin,
                             'End' => $End
                           )
                         ),
                         'Name' => $Name,
                         'Release' => $Release
                       ),
                       'Created' => $Created,
                       'ID' => $ID
                     )

        );
  }
  catch(Exception $e)
  {
    $error = $e->__toString();
    fwrite(STDERR,"Failed to GetReport from $ServiceProvider: $error\n");
    exit();
  }
}

$xml = $client->__getLastResponse();

$fname = $ServiceProvider.'_'.$Name.'_'.$Begin.'_'.$End.'.xml';
$replace="_";
$pattern="/([[:alnum:]_\.-]*)/";
$fname = $SSFileStore .'/' . str_replace(str_split(preg_replace($pattern,$replace,$fname)),$replace,$fname);
file_put_contents($fname, $xml);
$msg = "Report saved: $fname";

fwrite(STDOUT,"$Name retrieved from $ServiceProvider for the period from $Begin to $End\n");
fwrite(STDOUT,"Check out the contents of '$fname'\n");

?>
