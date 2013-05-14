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
      <h2>Delete service provider</h2>
        <form action="" method="post">
          <p>Service Provider: <?php echo $ServiceProvider; ?></p>
          <p>Service URL: <?php echo $ServiceURL; ?></p>
          <p>WSDL: <?php echo $WSDLURL; ?></p>
          <p>Service Type: <?php echo $ServiceType; ?></p>
          <p>Security : <?php echo $Security; ?></p>
          <?php if (!empty($Login)): ?>
            <p>Login: <?php echo $Login; ?></p>
            <p>Password: <?php echo $Password; ?></p>
          <?php endif; ?>
          <p>RequestorID: <?php echo $RequestorID; ?></p>
          <p>RequestorName: <?php echo $RequestorName; ?></p>
          <p>Requestor email: <?php echo $RequestorEmail; ?></p>
          <p>CustomerReferenceID: <?php echo $CustomerReferenceID; ?></p>
          <p>CustomerReferenceName: <?php echo $CustomerReferenceName; ?></p>
          <p>Notes: <?php echo $Instructions; ?></p>
            <input type="hidden" name="frmPID" value="<?php echo $PID; ?>"/>
            <input type="hidden" name="frmProviderName" value="<?php echo $ServiceProvider; ?>"/>
            <input type="hidden" name="deleteService" value="Delete service"/>
            <input type="submit" name="deleteService"  class="fm-submit" value="Delete Service"/>
        </form>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>