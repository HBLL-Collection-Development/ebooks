<?php

if (isset($_POST['PlatformCode']))
{
  $PlatformCode = $_POST['PlatformCode'];
}
else
{
  $PlatformCode = $Extensions['PlatformCode'];
}

$identifier =  substr($CustomerReferenceID, -5, 5);

try {
  $client = new SoapClient($WSDL,array
                                 (
                                   "trace"      => 1,
                                   "exceptions" => 1
                                 )
                          );
    } 
    catch (Exception $e)
    {                           
        $error = $e->__toString();
         include 'err.html.php';
         exit();
    }

// Prepare SoapHeader parameters 
$sh_param->TransId = 'transid'; 
$sh_param->ReqId = '1'; 
$sh_param->Ver = '1'; 
$sh_param->Consumer = 'SCIDIR'; 
$sh_param->ConsumerClient = $RequestorName; 
$sh_param->LogLevel = 'All'; 
$headers = new SoapHeader('http://webservices.elsevier.com/schemas/easi/headers/types/v1', 'EASIReq', $sh_param, false);
 
// Prepare Soap Client 
try {
  $client->__setSoapHeaders($headers); 
}
catch (Exception $e)
{
         $error = $e->__toString();
         include 'err.html.php';
         exit();
}

try {
  $result = $client->GetReport(
                    array
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
                        'ID' => '1',
                        'authenticationWrapper' => array
                        (
                            'endUserId' => array
                            (
                                'identifier' => $identifier,
                                'identifierType' => "accountId"
                            ),
                            'integratorId' => $RequestorID,
                            'platformCode' => $PlatformCode
                        ) 
                    )
  );
}
catch (Exception $e)
{
         $error = $e->__toString();
         include 'err.html.php';
         exit();
}

 
?>