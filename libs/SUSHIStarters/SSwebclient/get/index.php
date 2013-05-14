<?php

  include_once '../../SSIncludes/helpers.inc.php';

  if (isset($_POST['getReport']))
  {

    $Extension = '';
    $error = '';
    $msg = '';
    $Login = '';
    $Password = '';

    $ServiceProvider = html($_POST['frmProviderName']);
    $Name = html($_POST['frmReportName']);
    $ServiceURL = html($_POST['frmServiceURL']);
    $WSDLURL = html($_POST['frmWSDLURL']);
    $RequestorID = html($_POST['frmRequestorID']);
    $RequestorName = html($_POST['frmRequestorName']);
    $RequestorEmail = html($_POST['frmRequestorEmail']);
    $CustomerReferenceID = html($_POST['frmCustomerReferenceID']);
    $CustomerReferenceName = html($_POST['frmCustomerReferenceName']);
    $Release = html($_POST['frmReportRelease']);
    $Begin = html($_POST['frmFromY']).'-'.html($_POST['frmFromM']).'-01';
    $End = html($_POST['frmToY']).'-'.html($_POST['frmToM']).'-'.date('t',strtotime(html($_POST['frmToY']).'-'.html($_POST['frmToM']).'-01'));
    $ReportOutput = html(($_POST['frmSave']));
    $DebugOptions = ($_POST['frmDebug']);
    $Login = html($_POST['frmLogin']);
    $Password = html($_POST['frmPassword']);
    $Extension = html($_POST['Extension']);
    $Security = html($_POST['frmSecurity']);

    $Created = date("Y-m-d\TH:i:s.0\Z");
    $ID = uniqid($_SERVER['HTTP_HOST'].":", true);

    if (strtoupper($WSDLURL) != 'COUNTER')
    {
      $WSDL = $WSDLURL;
    }

    if (!empty($Extension))
    {
      include '../SSIncludes/extension_'.$Extension.'.inc.php';
    }
    else
    {

      if (preg_match("/http/i", $Security))
      {
        try {
          $client = new SoapClient($WSDL,array
            (
              'login'          => $Login,
              'password'       => $Password,
              'location' => $ServiceURL,
              "trace"      => 1,
              "exceptions" => 1,
            )
          );
        } catch (Exception $e) {
           $error = $e->__toString();
           include 'err.html.php';
           exit();
        }
      }
      else
      {
        if (strtoupper($WSDLURL) != 'COUNTER')
        {
          try {
            $client = new SoapClient($WSDL,array(
              "trace"      => 1,
              "exceptions" => 1));
          } catch (Exception $e) {
             $error = $e->__toString();
             include 'err.html.php';
             exit();
          }
        } else {
          try {
            $client = new SoapClient($WSDL,array(
              'location' => $ServiceURL,
              "trace"      => 1,
              "exceptions" => 1));
          } catch (Exception $e) {
             $error = $e->__toString();
             include 'err.html.php';
             exit();
          }
        }
      }

      if (preg_match("/wsse/i", $Security))
      {
        // Prepare SoapHeader parameters
        class clsWSSEAuth {
            private $Username;
            private $Password;
          function __construct($username, $password)
          {
            $this->Username=$username;
            $this->Password=$password;
          }
        }

        class clsWSSEToken {
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
        try {
          $client->__setSoapHeaders(array($objSoapVarWSSEHeader));
        }
        catch (Exception $e)
        {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

      }

      try{
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
           include 'err.html.php';
           exit();
      }
    }

    $xml = $client->__getLastResponse();

    $LastRequestHeaders = '';
    $LastRequest = '';
    $LastResponseHeaders = '';

    if(!empty($DebugOptions))
    {
      foreach ($DebugOptions as $DebugOption)
      {
        if($DebugOption == 'lastRequestHeaders')
        {
           $LastRequestHeaders = $client->__getLastRequestHeaders();
        }
        if($DebugOption == 'lastRequest')
        {
           $LastRequest = $client->__getLastRequest();
        }
        if($DebugOption == 'lastResponseHeaders')
        {
           $LastResponseHeaders = $client->__getLastResponseHeaders();
        }
      }
    }

    if ($ReportOutput == 'save')
    {
      $fname = $ServiceProvider.'_'.$Name.'_'.$Begin.'_'.$End.'.xml';
      $replace="_";
      $pattern="/([[:alnum:]_\.-]*)/";
      $fname = $SSFileStore .'/' . str_replace(str_split(preg_replace($pattern,$replace,$fname)),$replace,$fname);
      file_put_contents($fname, $xml);
      $msg = "Report saved: $fname";
    }
    elseif ($ReportOutput == 'download')
    {
      $fname = $ServiceProvider.'_'.$Name.'_'.$Begin.'_'.$End.'.xml';
      $replace="_";
      $pattern="/([[:alnum:]_\.-]*)/";
      $fname = str_replace(str_split(preg_replace($pattern,$replace,$fname)),$replace,$fname);
      header("Content-type: text/xml");
      header("Content-Disposition: attachment; filename=$fname");
      echo $xml;
      exit();
    }

    include 'getreport.html.php';
    exit();
  }

  loadServices($SSServicesCSV);

  if (isset($_POST['frmTest']))
  {
    $PID = $_POST['frmTestServiceProvider'];
  }
  elseif (isset($_POST['frmService']))
  {
    $PID = $_POST['frmServiceProvider'];
  }
  else
  {
    include 'setservice.html.php';
    exit();
  }

  setServiceVars('Service', $PID);

  $Extensions = array();

  // look at $Security to ses if it uses an extension
  if(preg_match('/Extension=/i', $Security))
  {
    $varlist = explode(";", $Security);
    foreach( $varlist as $params)
    {
      list($extVar, $extVal) = explode("=", $params);
      $Extensions[$extVar] = $extVal;
    }
  }






  include 'requestform.html.php';

?>