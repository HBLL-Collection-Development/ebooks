<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>SUSHI</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="<?php echo $SSRoot; ?>sushistarters.css" type="text/css" />
  </head>
  <body id="tab1">
    <div id="body-container">
      <?php include '../SSIncludes/header.inc.html.php'; ?>
      <?php include '../SSIncludes/nav.inc.html.php'; ?>
      <h2>Welcome to the SUSHIStarters Client Version 1.0.0alpha1</h2>
      <h3>About the SUSHIStarters Client</h3>
      <p>The SUSHIStarters Client is a 'beginners', free, open source programme using a web-based user interface to support the downloading/retrieval of COUNTER-compliant SUSHI reports.</p>
      <p>SUSHIStarters consists of a series of web-forms and guidance notes that take you through the steps and parameters needed to connect successfully to
         SUSHI servers and download the reports of a number of major vendors.</p>
      <h4>Getting started</h4>
      <ul>
        <li>Go straight to the 'Get Report' tab and have a play with the SUSHIStarters Test Server, try different options and see what happens!</li>
        <li>Go to the 'Configure Services' tab, add a Service Provider - use an existing template to make life easier for yourself, or a blank template to start from scratch
            - and then go to the 'Get Report' tab, select the Provider you just added, select your required report options and hit the 'Get Report' button</li>
      </ul>
      <h3>About SUSHI</h3>
      <p>SUSHI (the Standardized Usage Statistics Harvesting Initiative) Protocol has been developed to enable the automated harvesting of usage data,
         replacing the time-consuming user-mediated collection of online usage reports. The SUSHI Protocol is designed to be both generalised and extensible,
         meaning it can be used to retrieve a variety of usage reports. An extension is designed specifically to work with the COUNTER usage reports,
         which are by far the most widely retrieved usage reports.</p>
      <p>In order to set up SUSHI harvesting to harvest statistics from a vendor you need:</p>
      <ul>
        <li>a SUSHI client (hopefully you'll find this one does the job for you!) or a SUSHI enabled ERM</li>
        <li>to obtain some details from the vendor (publisher, aggregator, etc.)<br /><br />Usually, the required information consists of a vendor URL, a Customer Reference ID and a Requestor ID but
         some vendors will also require you to provide other information. Vendors who offer SUSHI harvesting often place these
         details on your customer administration site, but this is not always the case, and you may need to contact the vendor
         directly to obtain the connection details.</li>
      </ul>
      <p>The <a href="http://www.niso.org/workrooms/sushi/">NISO SUSHI web pages</a> contain a wealth of information about SUSHI and are highly recommended reading!</p>
      <h4>SUSHI client developers</h4>
      <p>Check out the <a href="http://www.niso.org/workrooms/sushi/faq/librarian">Getting Started with SUSHI</a> page on the NISO website for a useful guide to all things SUSHI.
         If you need further help or advice email the <a href="http://www.niso.org/lists/sushidevelopers/">SUSHI developers email list</a> with your questions.</p>
      <h4>Librarians</h4>
      <p>Check out the NISO <a href="http://www.niso.org/workrooms/sushi/faq/librarian">FAQ for librarians</a> for more information about the protocol.
         If you still need help after reading through the FAQ and other resources on the site, don't hesitate to email the
         <a href="http://www.niso.org/lists/sushidevelopers/">SUSHI developers email list</a> with your questions.</p>


      <?php include '../SSIncludes/footer.inc.html.php'; ?>
    </div>
  </body>
</html>
