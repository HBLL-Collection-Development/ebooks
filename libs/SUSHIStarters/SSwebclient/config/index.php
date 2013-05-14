<?php

  include_once '../../SSIncludes/helpers.inc.php';

  loadServices($SSServicesCSV);
  loadServices($SSTemplatesCSV);

  $msg = '';
  $fields = array();

  if (isset($_POST['addService']))
  {

    if (!copy($SSServicesCSV, "$SSServicesCSV." . date('YmdHis') . ".bak")) {
      echo "failed to copy $file...\n";
    }

    setServiceVars('Posts', '');

    // if exact match for ServiceProvider is already in array append unique datetime to avoid overwriting existing entry
    foreach ($serviceParamsArray as $serviceParams) {
      if ($ServiceProvider == $serviceParams['Provider'])
      {
        $ServiceProvider = $ServiceProvider . ' (' . date('Y-m-d H:i:s') . ')';
        break;
      }
    }

    $fields = array($ServiceType, $ServiceProvider, $ReportNames, $ServiceURL, $WSDLURL, $RequestorID, $RequestorName, $RequestorEmail, $CustomerReferenceID, $CustomerReferenceName, $Security, $Login, $Password, $Instructions);
    $fp = fopen($SSServicesCSV, 'a');
    fputcsv($fp, $fields);
    fclose($fp);
    $msg = "$ServiceProvider successfully added to configuration.";
    include 'config.html.php';
    exit();
  }

  if (isset($_POST['addPickService']))
  {
    $PID = html($_POST['frmTemplate']);
    setServiceVars('Template', $PID);
    include 'add.html.php';
    exit();
  }

  if (isset($_POST['deleteService']))
  {

    if (!copy($SSServicesCSV, "$SSServicesCSV." . date('YmdHis') . ".bak")) {
      echo "failed to copy $file...\n";
    }

    $PID = html($_POST['frmPID']);
    $ServiceProvider = html($_POST['frmProviderName']);
    $fieldnames = array('Category',' Service Provider','Reports','Service URL','WSDL URL','RequestorID','RequestorName','RequestorEmail','CustomerID','CustomerName','Security','Login','Password','Instructions');
    $fp = fopen($SSServicesCSV, 'w');
    fputcsv($fp, $fieldnames);
    foreach ($serviceParamsArray as $serviceParams) {
      if ($ServiceProvider != $serviceParams['Provider'])
      {
        fputcsv($fp, $serviceParams);
      }
    }
    fclose($fp);
    $msg = "$ServiceProvider successfully deleted from configuration.";
    include 'config.html.php';
    exit();
  }

  if (isset($_POST['deletePickService']))
  {
    $PID = html($_POST['frmService']);
    setServiceVars('Service', $PID);
    include 'delete.html.php';
    exit();
  }

  if (isset($_POST['deletePickTestService']))
  {
    $PID = html($_POST['frmTestService']);
    setServiceVars('Service', $PID);
    include 'delete.html.php';
    exit();
  }

  if (isset($_POST['updateService']))
  {

    if (!copy($SSServicesCSV, "$SSServicesCSV." . date('YmdHis') . ".bak")) {
      echo "failed to copy $file...\n";
    }

    $OrigSP = html($_POST['frmOrigSP']);
    setServiceVars('Posts', '');

    $fieldnames = array('Category',' Service Provider','Reports','Service URL','WSDL URL','RequestorID','RequestorName','RequestorEmail','CustomerID','CustomerName','Security','Login','Password','Instructions');
    $fields = array($ServiceType, $ServiceProvider, $ReportNames, $ServiceURL, $WSDLURL, $RequestorID, $RequestorName, $RequestorEmail, $CustomerReferenceID, $CustomerReferenceName, $Security, $Login, $Password, $Instructions);
    $fp = fopen($SSServicesCSV, 'w');
    fputcsv($fp, $fieldnames);
    foreach ($serviceParamsArray as $serviceParams) {
      if ($OrigSP == $serviceParams['Provider'])
      {
       $serviceParams = $fields;
      }
      fputcsv($fp, $serviceParams);
    }
    fclose($fp);
    $msg = "$ServiceProvider configuration successfully updated.";
    include 'config.html.php';
    exit();
  }

  if (isset($_POST['editPickService']))
  {
    $PID = html($_POST['frmService']);
    setServiceVars('Service', $PID);
    include 'edit.html.php';
    exit();
  }

  if (isset($_POST['editPickTestService']))
  {
    $PID = html($_POST['frmTestService']);
    setServiceVars('Service', $PID);
    include 'edit.html.php';
    exit();
  }

  if (isset($_POST['frmTask']))
  {
    $task = $_POST['frmTask'];

    if ($task == 'add')
    {
      include 'addpick.html.php';
    }
    elseif ($task == 'edit')
    {
      include 'editpick.html.php';
    }
    elseif ($task == 'delete')
    {
      include 'deletepick.html.php';
    }
    else
    {
      // something has gone badly wrong!
    }
    exit();
  }

  include 'config.html.php';

?>