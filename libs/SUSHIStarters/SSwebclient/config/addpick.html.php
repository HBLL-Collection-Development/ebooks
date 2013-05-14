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
      <h2>Add new service provider</h2>
      <div id="fm-container">
        <form name="serviceAdd" action="" method="post">
          <label for="frmTemplate">Service template: </label>
          <select name='frmTemplate' size="6">
            <option value='0'>New empty template</option>
             <?php foreach ($templateServiceParamsArray as $key => $template): ?>
              <option value='<?php echo $key; ?>'><?php echo $template['Provider']; ?></option>
             <?php endforeach; ?>
          </select>
          <div id="fm-submit">
            <input type="submit" name="addPickService"  class="fm-submit" value="Next"/>
          </div>
        </form>
      </div>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>