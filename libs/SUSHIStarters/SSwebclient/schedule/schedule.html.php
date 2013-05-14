<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>SUSHI</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="<?php echo $SSRoot; ?>sushistarters.css" type="text/css" />
  </head>
  <body id="tab6">
    <div id="body-container">
      <?php include '../../SSIncludes/header.inc.html.php'; ?>
      <?php include '../../SSIncludes/nav.inc.html.php'; ?>
      <h2>Scheduling reports</h2>
      <p>You can't directly schedule reports from this web-based client, but SUSHIStarters ships with a command line tool
         suitable for use with 'cron' (unix-based systems) and 'Microsoft Task Scheduler' (Windows).</p>
      <p>Depending on how your system is set up, you can invoke the tool using either</p>
      <pre>php SUSHIStartersCLI.php "Service Provider" reporttype daterange</pre>
      <p>Or</p>
      <pre>SUSHIStartersCLI.php "Service Provider" reporttype daterange</pre>
      <p>The CLI tool requires you to supply three arguments:</p>
      <pre><ol><li>"Service Provider": Name of a Service Provider exactly as you configured it, surrounded by quotes</li><li>reporttype: Type of report you want, e.g. JR1, JR1a, BR3</li><li>daterange: either YYYY-MM:YYYY-MM or lastmonth</li></ol></pre>
      <p>You'll find the tool in the SUSHIStarters scripts directory.</p>
      <?php include '../../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>
