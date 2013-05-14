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
      <div id="fm-container">
        <form name="productionDelete" action="" method="post">
          <fieldset>
            <legend>Production Services</legend>
            <p><label for="frmService">Service provider: </label>
            <select name='frmService' size="6">
              <?php foreach ($serviceParamsArray as $key => $service): ?>
                <?php if ($service['Type'] == 'Service'): ?>
                  <option value='<?php echo $key; ?>'><?php echo $service['Provider']; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select></p>
            <input type="hidden" name="frmServiceType"  value="Production"/>
            <input type="submit" name="deletePickService"  class="fm-submit" value="Next"/>
          </fieldset>
        </form>
        <form name="testDelete" action="" method="post">
          <fieldset>
            <legend>Test Services</legend>
            <p><label for="frmTestService">Test service provider: </label>
            <select name='frmTestService' size="6">
              <?php foreach ($serviceParamsArray as $key => $service): ?>
                <?php if ($service['Type'] == 'Test'): ?>
                  <option value='<?php echo $key; ?>'><?php echo $service['Provider']; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select></p>
            <input type="hidden" name="frmServiceType"  value="Test"/>
            <input type="submit" name="deletePickTestService"  class="fm-submit" value="Next"/>
          </fieldset>
        </form>
      </div>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>