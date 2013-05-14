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
      <p><?php echo $msg; ?></p>
      <div id="fm-container">
        <form action="" method="post">
          <fieldset>
            <legend>Choose task</legend>
              <input type="radio" name="frmTask" value="add" />&nbsp;Add new service
              <input type="radio" name="frmTask" value="edit" />&nbsp;Edit service
              <input type="radio" name="frmTask" value="delete" />&nbsp;Delete service
          </fieldset>
          <input type="submit" name="frmService" class="fm-submit" value="Next"/>
        </form>
      </div>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>
