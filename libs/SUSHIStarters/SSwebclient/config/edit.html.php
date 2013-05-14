<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>SUSHI</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="<?php echo $SSRoot; ?>sushistarters.css" type="text/css" />
  </head>
  <body id="tab2">
    <div id="body-container">
      <?php include '../../SSIncludes/header.inc.html.php'; ?>
      <?php include '../../SSIncludes/nav.inc.html.php'; ?>
      <h2>Edit service provider</h2>
      <div id="fm-container">
        <form action="" method="post">
          <input type="hidden" name="frmMode" value="edit"/>
          <input type="hidden" name="frmOrigSP" value="<?php echo $ServiceProvider; ?>" />
          <fieldset>
            <legend>Service Provider</legend>
              <p><label for="frmProviderName">Provider Name:</label>
                 <input type="text" class="wide-input" name="frmProviderName" value="<?php echo $ServiceProvider; ?>" /></p>
              <p><label for="frmServiceURL">Service URL:</label>
                 <input type="text" class="wide-input" name="frmServiceURL" value="<?php echo $ServiceURL; ?>" /></p>
              <p><label for="frmWSDLURL">WSDL:</label>
                 <input type="text" class="wide-input" name="frmWSDLURL" value="<?php echo $WSDLURL; ?>" /></p>
                <fieldset>
                  <legend>Service type</legend>
                  <input type="radio" name="frmServiceType" id="frmServiceType" value="Service"
                    <?php if ($ServiceType == 'Service') echo "checked"; ?> /> Production service&nbsp;
                  <input type="radio" name="frmServiceType" id="frmServiceType" value="Test"
                    <?php if ($ServiceType == 'Test') echo "checked"; ?> /> Test service&nbsp;
                </fieldset>
          </fieldset>
          <fieldset>
            <legend>Security</legend>
                  <input type="radio" name="frmSecurity" id="frmSecurity" value="None"
                    <?php if ($Security == 'None') echo "checked"; ?> /> None<br />
                  <input type="radio" name="frmSecurity" id="frmSecurity" value="Restricted by IP address"
                    <?php if ($Security == 'Restricted by IP address') echo "checked"; ?> /> Restricted by IP address<br />
                  <input type="radio" name="frmSecurity" id="frmSecurity" value="HTTP Basic Authentication"
                    <?php if ($Security == 'HTTP Basic Authentication') echo "checked"; ?> /> HTTP Basic Authentication&nbsp;
                  <input type="radio" name="frmSecurity" id="frmSecurity" value="WSSE Authentication"
                    <?php if ($Security == 'WSSE Authentication') echo 'checked'; ?> /> WSSE Authentication
                <fieldset>
                  <legend>Authentication Credentials</legend>
                    <label>Username: <input type="text" name="frmLogin" value="<?php echo $Login; ?>" /></label>
                    <label>Password: <input type="text" name="frmPassword" value="<?php echo $Password; ?>" /></label>
                </fieldset>
                  <input type="radio" name="frmSecurity" id="frmSecurity" value="Extension"
                    <?php if (preg_match('/Extension=/i', $Security)) echo "checked"; ?> /> Extension&nbsp;<br />
                <fieldset>
                  <legend>Extension Parameters</legend>
                  <textarea name="frmExtensionParams" id="frmExtensionParams" class="extra-wide-input"><?php if (preg_match('/Extension=/i', $Security)) echo $Security; ?></textarea>
                </fieldset>
          </fieldset>
          <fieldset>
              <legend>Requestor</legend>
              <p><label for="frmRequestorID">RequestorID:</label>
                 <input type="text" class="wide-input" name="frmRequestorID" value="<?php echo $RequestorID; ?>" /></p>
              <p><label for="frmRequestorName">RequestorName:</label>
                 <input type="text" class="wide-input" name="frmRequestorName" value="<?php echo $RequestorName; ?>" /></p>
              <p><label for="frmRequestorEmail">Requestor email:</label>
                 <input type="text" class="wide-input" name="frmRequestorEmail" value="<?php echo $RequestorEmail; ?>" /></p>
          </fieldset>
          <fieldset>
              <legend>CustomerReference</legend>
              <p><label for="frmCustomerReferenceID">CustomerReferenceID:</label>
                 <input type="text" class="wide-input" name="frmCustomerReferenceID" value="<?php echo $CustomerReferenceID; ?>" /></p>
              <p><label for="frmCustomerReferenceName">CustomerReferenceName:</label>
                 <input type="text" class="wide-input" name="frmCustomerReferenceName" value="<?php echo $CustomerReferenceName; ?>" /></p>
          </fieldset>
          <fieldset>
            <legend>COUNTER Reports Available</legend>
                <fieldset>
                  <legend>Book Reports (Release 1)</legend>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="BR1"
                       <?php if (in_array("BR1", $Reports)) echo "checked"; ?> />BR1</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="BR2"
                       <?php if (in_array("BR2", $Reports)) echo "checked"; ?> />BR2</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="BR3"
                       <?php if (in_array("BR3", $Reports)) echo "checked"; ?> />BR3</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="BR4"
                       <?php if (in_array("BR4", $Reports)) echo "checked"; ?> />BR4</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="BR5"
                       <?php if (in_array("BR5", $Reports)) echo "checked"; ?> />BR5</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="BR6"
                       <?php if (in_array("BR6", $Reports)) echo "checked"; ?> />BR6</label>
                </fieldset>
                <fieldset>
                  <legend>Consortium Reports (Release 3)</legend>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="CR1"
                       <?php if (in_array("CR1", $Reports)) echo "checked"; ?> />CR1</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="CR2"
                       <?php if (in_array("CR2", $Reports)) echo "checked"; ?> />CR2</label>
                </fieldset>
                <fieldset>
                  <legend>Database Reports (Release 3)</legend>
                     <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="DB1"
                       <?php if (in_array("DB1", $Reports)) echo "checked"; ?> />DB1</label>
                     <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="DB2"
                       <?php if (in_array("DB2", $Reports)) echo "checked"; ?> />DB2</label>
                     <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="DB3"
                       <?php if (in_array("DB3", $Reports)) echo "checked"; ?> />DB3</label>
                </fieldset>
                <fieldset>
                  <legend>Journal Reports (Release 3)</legend>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="JR1"
                       <?php if (in_array("JR1", $Reports)) echo "checked"; ?> />JR1</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="JR1a"
                       <?php if (in_array("JR1a", $Reports)) echo "checked"; ?> />JR1a</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="JR2"
                       <?php if (in_array("JR2", $Reports)) echo "checked"; ?> />JR2</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="JR3"
                       <?php if (in_array("JR3", $Reports)) echo "checked"; ?> />JR3</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="JR4"
                       <?php if (in_array("JR4", $Reports)) echo "checked"; ?> />JR4</label>
                    <label class="checkbox"><input type="checkbox" name="frmReportName[]" id="frmReportName" value="JR5"
                       <?php if (in_array("JR5", $Reports)) echo "checked"; ?> />JR5</label>
                </fieldset>

          </fieldset>
          <fieldset>
            <legend>Instructions</legend>
              <label for="frmInstructions">Notes on service:</label>
              <textarea name="frmInstructions" id="frmInstructions"><?php echo $Instructions; ?></textarea>
          </fieldset>
          <input type="hidden" name="updateService" value="Update service"/>
          <input type="submit" name="updateService"  class="fm-submit" value="Update Service"/>
        </form>
      </div>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>