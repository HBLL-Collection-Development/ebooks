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
        <div id="fm-container">
          <form action="" method="post">
          <fieldset>
            <legend>Production Services</legend>
              <p><label for="frmServiceProvider">Service provider: </label>
              <select name='frmServiceProvider' size="6">
                <?php foreach ($serviceParamsArray as $key => $service): ?>
                  <?php if ($service['Type'] == 'Service'): ?>
                    <option value='<?php echo $key; ?>'><?php echo $service['Provider']; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select></p>
                <input type="submit" name="frmService" class="fm-submit" value="Next"/>
            </fieldset>
            <fieldset>
              <legend>Test Services</legend>
              <p><label for="frmTestServiceProvider">Test service provider: </label>
              <select name='frmTestServiceProvider' size="6">
                <?php foreach ($serviceParamsArray as $key => $service): ?>
                  <?php if ($service['Type'] == 'Test'): ?>
                    <option value='<?php echo $key; ?>'><?php echo $service['Provider']; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select></p>
              <input type="submit" name="frmTest" class="fm-submit" value="Next"/>
            </fieldset>
          </form>
        </div>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>
