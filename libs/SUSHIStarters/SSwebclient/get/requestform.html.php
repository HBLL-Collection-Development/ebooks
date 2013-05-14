<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>SUSHI</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="<?php echo $SSRoot; ?>sushistarters.css" type="text/css" />
  </head>
  <body id="tab4">
    <div id="body-container">
      <?php include '../../SSIncludes/header.inc.html.php'; ?>
      <?php include '../../SSIncludes/nav.inc.html.php'; ?>
      <h2>SUSHI COUNTER Report Request</h2>

        <form action="" method="post">
      <div id="fm-container">
          <fieldset>
            <legend>Service Provider</legend>
              <p><label for="frmProviderName">Provider Name:</label>
                 <input type="text" class="wide-input" name="frmProviderName" value="<?php echo $ServiceProvider; ?>" /></p>
              <p><label for="frmServiceURL">Service URL:</label>
                 <input type="text" class="wide-input" name="frmServiceURL" value="<?php echo $ServiceURL; ?>" /></p>
              <p><label for="frmWSDLURL">WSDL:</label>
                 <input type="text" class="wide-input" name="frmWSDLURL" value="<?php echo $WSDLURL; ?>" /></p>
          </fieldset>
          <fieldset>
            <legend>Security</legend>
              <?php if (!empty($Extensions)): ?>
                 <?php foreach ($Extensions as $key => $value): ?>
                <p><label for="<?php echo $key; ?>"><?php echo $key; ?>:</label>
                  <input type="text" class="wide-input" name="<?php echo $key; ?>" value="<?php echo $value; ?>" /></p>
                 <?php endforeach; ?>
              <?php else: ?>
                <input type="hidden" name="frmSecurity" value="<?php echo $Security; ?>" />
                <p><?php echo $Security; ?></p>
              <?php endif; ?>
              <?php if ( (!empty($Login)) || (!empty($Password)) ): ?>
                <p><label for="frmLogin">Username:</label>
                  <input type="text" class="wide-input" name="frmLogin" value="<?php echo $Login; ?>" /></p>
                <p><label for="frmPassword">Password:</label>
                  <input type="text" class="wide-input" name="frmPassword" value="<?php echo $Password; ?>" /></p>
              <?php endif; ?>
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
            <legend>ReportDefinition</legend>
            <p><label for="frmReportName">Name:</label>

                 <?php foreach ($Reports as $ReportName): ?>
                  <input type="radio" name="frmReportName" value="<?php echo $ReportName; ?>" checked />&nbsp;<?php echo $ReportName; ?>&nbsp;
                 <?php endforeach; ?>

            <p><label for="frmReportRelease">Release:</label>
              <input type="radio" name="frmReportRelease" id="frmReportRelease" value="1" /> 1&nbsp;
              <input type="radio" name="frmReportRelease" id="frmReportRelease" value="2" /> 2&nbsp;
              <input type="radio" name="frmReportRelease" id="frmReportRelease" value="3" checked /> 3&nbsp;
              <fieldset>
                <legend>UsageDateRange</legend>
            <div class="floatleft">
              <p><label for="frmFromM">Begin: </label>
              <select name='frmFromM'>
                <option value="01">Jan</option>
                <option value="02">Feb</option>
                <option value="03">Mar</option>
                <option value="04">Apr</option>
                <option value="05">May</option>
                <option value="06">Jun</option>
                <option value="07">Jul</option>
                <option value="08">Aug</option>
                <option value="09">Sep</option>
                <option value="10">Oct</option>
                <option value="11">Nov</option>
                <option value="12">Dec</option>
              </select>&nbsp;
              <select name='frmFromY'>
                 <?php for ($i = 0; $i <= 4; $i++): ?>
                  <option value="<?php echo $YYYY-$i; ?>"><?php echo $YYYY-$i; ?></option>
                 <?php endfor; ?>
              </select></p>
            </div>
            <div class="floatleft">
              <p><label for="frmToM">End: </label>
              <select name='frmToM'>
                <option value="01">Jan</option>
                <option value="02">Feb</option>
                <option value="03">Mar</option>
                <option value="04">Apr</option>
                <option value="05">May</option>
                <option value="06">Jun</option>
                <option value="07">Jul</option>
                <option value="08">Aug</option>
                <option value="09">Sep</option>
                <option value="10">Oct</option>
                <option value="11">Nov</option>
                <option value="12">Dec</option>
              </select>&nbsp;
              <select name='frmToY'>
                <?php for ($i = 0; $i <= 4; $i++): ?>
                  <option value="<?php echo $YYYY-$i; ?>"><?php echo $YYYY-$i; ?></option>
                 <?php endfor; ?>
              </select></p>
            </div>
            </fieldset>
          </fieldset>
          <fieldset>
            <legend>Runtime Options</legend>
            <fieldset>
              <legend>Save report</legend>
              <input type="radio" name="frmSave" value="save" />&nbsp;Save in SUSHIStarters filestore<br />
              <input type="radio" name="frmSave" value="download" />&nbsp;Download to desktop<br />
            </fieldset>
            <fieldset>
              <legend>Debug</legend>
              <input type="radio" name="frmSave" value="view" checked />&nbsp;Show Report Response<br />
              <input type="checkbox" name="frmDebug[]" id="frmDebug" value="lastRequestHeaders" />&nbsp;Show Report Request Headers<br />
              <input type="checkbox" name="frmDebug[]" id="frmDebug" value="lastRequest" />&nbsp;Show Report Request<br />
              <input type="checkbox" name="frmDebug[]" id="frmDebug" value="lastResponseHeaders" />&nbsp;Show Report Response Headers
            </fieldset>
          </fieldset>
          <div id="fm-submit">
            <input type="hidden" name="getReport" value="Get Report"/>
            <input type="submit" name="getReport"  class="fm-submit" value="Get Report"/>
          </div>
      </div>
      <div id="sidebar">
        <h3>Notes</h3>
        <p><?php echo $Instructions; ?></p>
      </div>
        </form>

      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>