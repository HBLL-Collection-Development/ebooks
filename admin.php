<?php
/**
  * Admin page to add usage to database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-05-15
  *
  */
require_once 'config.php';

$html = <<<HTML
<h1>Project COUNTER Book Report #1</h1>
<form enctype="multipart/form-data" action="ingest.php" method="post">
    <p><input type="file" name="uploadedfile"/></p>
    <input type="hidden" name="report" value="1" id="report"/>
    <p><input type="submit" value="Upload file"/></p>
</form>

<h1>Project COUNTER Book Report #2</h1>
<form enctype="multipart/form-data" action="ingest.php" method="post">
    <p><input type="file" name="uploadedfile"/></p>
    <input type="hidden" name="report" value="2" id="report"/>
    <p><input type="submit" value="Upload file"/></p>
</form>
HTML;

template::display('generic.tmpl', $html);

?>
