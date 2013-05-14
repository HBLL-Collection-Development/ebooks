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
      <h2>Service Provider: <?php echo $ServiceProvider; ?></h2>

    <?php if (!empty($msg)): ?>
      <p><?php echo $msg; ?></p>
    <?php endif; ?>

    <?php if (!empty($LastRequestHeaders)): ?>
      <h3>SUSHI COUNTER Report Request Headers</h3>
      <pre><?php echo $LastRequestHeaders; ?></pre>
    <?php endif; ?>

    <?php if (!empty($LastRequest)): ?>
      <h3>SUSHI COUNTER Report Request</h3>
      <pre><?php echo xmlpp($LastRequest, true); ?></pre>
    <?php endif; ?>

    <?php if (!empty($LastResponseHeaders)): ?>
      <h3>SUSHI COUNTER Report Response Headers</h3>
      <pre><?php echo $LastResponseHeaders; ?></pre>
    <?php endif; ?>

    <?php if ($ReportOutput == 'view'): ?>
      <h3>SUSHI COUNTER Report Response</h3>
      <pre><?php echo xmlpp($xml, true); ?></pre>
    <?php endif; ?>

      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>
