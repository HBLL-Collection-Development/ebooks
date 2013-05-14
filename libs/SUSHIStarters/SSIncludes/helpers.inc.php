<?php

// ***** Config *******************************************

  // URL of the web client - keep the trailing slash!
  $SSRoot = 'http://localhost:8888/books/libs/SUSHIStarters/SSwebclient/';

  // Path to SUSHIStarters directory containing the filestore, params and CLI scripts - keep the trailing slash!
  // This should *not* be directly available via the web browser
  $SSPath = '/Applications/MAMP/htdocs/books/libs/SUSHIStarters/';

// ***** Variables *******************************************

  $SSFileStore = $SSPath . 'filestore';
  $SSServicesCSV = $SSPath . 'params/SSServices.csv';
  $SSTemplatesCSV = $SSPath . 'params/SSTemplates.csv';

  date_default_timezone_set('UTC');
  $YYYY = date("Y");

  $templateServiceParamsArray= array();
  $serviceParamsArray= array();

  $WSDL = 'http://www.niso.org/schemas/sushi/counter_sushi3_0.wsdl';

// ***** Functions *******************************************

function html($text)
{
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function htmlout($text)
{
  echo html($text);
}

function loadServices($csvfile)
{

  global $templateServiceParamsArray;
  global $serviceParamsArray;

  $rowNum = 1;
  if (($handle = fopen($csvfile, "r")) !== FALSE)
  {
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
    {

      if ($data[0] == 'Template')
      {
        $templateServiceParamsArray[$rowNum] = array( 
                                      'Type' => $data[0],
                                      'Provider' => $data[1],
                                      'Reports' => $data[2], 
                                      'ServiceURL' => $data[3], 
                                      'WSDLURL' => $data[4], 
                                      'RequestorID' => $data[5],
                                      'RequestorName' => $data[6],
                                      'RequestorEmail' => $data[7],
                                      'CustomerID' => $data[8],
                                      'CustomerName' => $data[9],
                                      'Security' => $data[10],
                                      'Login' => $data[11],
                                      'Password' => $data[12],
                                      'Instructions' => $data[13]
                                      );
      }
      else
      {
        if (($data[0] == 'Service') || ($data[0] == 'Test'))
        {
          $serviceParamsArray[$rowNum] = array( 
                                      'Type' => $data[0],
                                      'Provider' => $data[1],
                                      'Reports' => $data[2], 
                                      'ServiceURL' => $data[3], 
                                      'WSDLURL' => $data[4], 
                                      'RequestorID' => $data[5],
                                      'RequestorName' => $data[6],
                                      'RequestorEmail' => $data[7],
                                      'CustomerID' => $data[8],
                                      'CustomerName' => $data[9],
                                      'Security' => $data[10],
                                      'Login' => $data[11],
                                      'Password' => $data[12],
                                      'Instructions' => $data[13]
                                      );
        }
      }
      $rowNum++;
    }
    fclose($handle);

    // sort arrays alphabetically by Provider
    uasort($serviceParamsArray, 'compareProvider');
    uasort($templateServiceParamsArray, 'compareProvider');

  }
}

function compareProvider($a, $b)
{
  return strnatcmp($a['Provider'], $b['Provider']);
}

function my_in_array( $needle, $haystack )
{
  if (is_array($haystack))
  {
    return in_array($needle, $haystack);
  }
  else // array is undefined
  {
    return false;
  }
}

function prettydate($date)
{
  list($yyyy, $mm, $dd) = explode("-", $date);
  return date("M-Y", mktime(0, 0, 0, $mm, $dd, $yyyy));  
}

function xmlpp($xml, $html_output=false) {   
   $xml_obj = new SimpleXMLElement($xml);   
   $level = 4;   
   $indent = 0; // current indentation level   
   $pretty = array();   
      
   // get an array containing each XML element   
   $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));   
 
   // shift off opening XML tag if present   
   if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {   
     $pretty[] = array_shift($xml);   
   }   
 
   foreach ($xml as $el) {   
     if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {   
         // opening tag, increase indent   
         $pretty[] = str_repeat(' ', $indent) . $el;   
         $indent += $level;   
     } else {   
       if (preg_match('/^<\/.+>$/', $el)) {               
         $indent -= $level;  // closing tag, decrease indent   
       }   
       if ($indent < 0) {   
         $indent += $level;   
       }   
       $pretty[] = str_repeat(' ', $indent) . $el;   
     }   
   }      
   $xml = implode("\n", $pretty);      
   return ($html_output) ? htmlentities($xml) : $xml;   
}  

function setServiceVars($op, $id)
{

  global $serviceParamsArray, $templateServiceParamsArray;
  global $ServiceType, $ServiceProvider, $ServiceURL, $WSDLURL, $Security, $Login, $Password;
  global $RequestorID, $RequestorName, $RequestorEmail, $CustomerReferenceID, $CustomerReferenceName;
  global $Reports, $Instructions, $ExtensionParams, $ReportNames;

  if ($op == 'Posts')
  {
    $ServiceType = html($_POST['frmServiceType']);
    $ServiceProvider = html($_POST['frmProviderName']);
    $Reports = $_POST['frmReportName'];
    $ServiceURL = html($_POST['frmServiceURL']);
    $WSDLURL = html($_POST['frmWSDLURL']);
    $RequestorID = html($_POST['frmRequestorID']);
    $RequestorName = html($_POST['frmRequestorName']);
    $RequestorEmail = html($_POST['frmRequestorEmail']);
    $CustomerReferenceID = html($_POST['frmCustomerReferenceID']);
    $CustomerReferenceName = html($_POST['frmCustomerReferenceName']);
    $Security = html($_POST['frmSecurity']);
    $ExtensionParams = html($_POST['frmExtensionParams']);
    $Login = html($_POST['frmLogin']);
    $Password = html($_POST['frmPassword']);
    $Instructions = html($_POST['frmInstructions']);

    $ReportNames = '';
    foreach ($Reports as $Report)
    {
      $ReportNames .= "$Report;";
    }
    $ReportNames = substr($ReportNames,0,-1);

    if (!empty($ExtensionParams))
    {
      $Security = $ExtensionParams;
    }

  }

  if ($op == 'Service')
  {
    $ServiceType = $serviceParamsArray[$id]['Type'];
    $ServiceProvider = $serviceParamsArray[$id]['Provider'];
    $Reports = split(";", $serviceParamsArray[$id]['Reports']);
    $ServiceURL = $serviceParamsArray[$id]['ServiceURL'];
    $WSDLURL = $serviceParamsArray[$id]['WSDLURL'];
    $RequestorID = $serviceParamsArray[$id]['RequestorID'];
    $RequestorName = $serviceParamsArray[$id]['RequestorName'];
    $RequestorEmail = $serviceParamsArray[$id]['RequestorEmail'];
    $CustomerReferenceID = $serviceParamsArray[$id]['CustomerID'];
    $CustomerReferenceName = $serviceParamsArray[$id]['CustomerName'];
    $Security = $serviceParamsArray[$id]['Security'];
    $Login = $serviceParamsArray[$id]['Login'];
    $Password = $serviceParamsArray[$id]['Password'];
    $Instructions = $serviceParamsArray[$id]['Instructions'];
  }

  if ($op == 'Template')
  {
    if (!empty($id))
    {
      $ServiceType = $templateServiceParamsArray[$id]['Type'];
      $ServiceProvider = $templateServiceParamsArray[$id]['Provider'];
      $Reports = split(";", $templateServiceParamsArray[$id]['Reports']);
      $ServiceURL = $templateServiceParamsArray[$id]['ServiceURL'];
      $WSDLURL = $templateServiceParamsArray[$id]['WSDLURL'];
      $RequestorID = $templateServiceParamsArray[$id]['RequestorID'];
      $RequestorName = $templateServiceParamsArray[$id]['RequestorName'];
      $RequestorEmail = $templateServiceParamsArray[$id]['RequestorEmail'];
      $CustomerReferenceID = $templateServiceParamsArray[$id]['CustomerID'];
      $CustomerReferenceName = $templateServiceParamsArray[$id]['CustomerName'];
      $Security = $templateServiceParamsArray[$id]['Security'];
      $Login = $templateServiceParamsArray[$id]['Login'];
      $Password = $templateServiceParamsArray[$id]['Password'];
      $Instructions = $templateServiceParamsArray[$id]['Instructions'];
    }
    else
    {
      $ServiceType = '';
      $ServiceProvider = '';
      $Reports = '';
      $ServiceURL = '';
      $WSDLURL = '';
      $RequestorID = '';
      $RequestorName = '';
      $RequestorEmail = '';
      $CustomerReferenceID = '';
      $CustomerReferenceName = '';
      $Security  = '';
      $Login = '';
      $Password = '';
      $Instructions = '';
    }
  }
}

?>
