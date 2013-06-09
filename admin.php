<?php
/**
  * Admin page to add usage to database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-05
  * @since 2013-05-15
  *
  */
require_once 'config.php';

$html = <<<HTML
<div class="span-14 page append-1">
  <p>Things to know before loading usage data:</p>
  <ol class="link_list large">
    <li>This program assumes that an individual book is only listed once per platform. If you have usage data where a book is listed multiple times per platform (as can happen with book series such as those found in BioOne), you will need to manually combine usage for that book into a single entry for that platform.</li>
    <li>Only plain text, Microsoft formatted CSV files may be loaded.</li>
    <li><strong>Format for Project COUNTER Book Report #1</strong>:
      <ul>
        <li><strong>Column order for Revision 1</strong>: Title, Publisher, Platform, ISBN, ISSN, Annual Usage, Usage Year, Vendor Name</li>
        <li><strong>Column order for Revision 4</strong>: Title, Publisher, Platform, DOI, Proprietary Identifier, ISBN, ISSN, Annual Usage, Usage Year, Vendor Name</li>
      </ul>
    </li>
    <li><strong>Format for Project COUNTER Book Report #2</strong>:
      <ul>
        <li><strong>Column order for Revision 1</strong>: Title, Publisher, Platform, ISBN, ISSN, Annual Usage, Usage Year, Vendor Name</li>
        <li><strong>Column order for Revision 4</strong>: Title, Publisher, Platform, DOI, Proprietary Identifier, ISBN, ISSN, Annual Usage, Usage Year, Vendor Name</li>
      </ul>
    </li>
  </ul>
</div>
<div class="span-7 page">
  <h1>Project COUNTER Book Report #1</h1>
  <form enctype="multipart/form-data" action="ingest.php" method="post">
      <p><input type="file" name="uploadedfile"/></p>
      <input type="hidden" name="report" value="1" id="report"/>
      <p><input type="submit" value="Upload file"/></p>
  </form>

  <h1 class="h2">Project COUNTER Book Report #2</h1>
  <form enctype="multipart/form-data" action="ingest.php" method="post">
      <p><input type="file" name="uploadedfile"/></p>
      <input type="hidden" name="report" value="2" id="report"/>
      <p><input type="submit" value="Upload file"/></p>
  </form>
</div>
HTML;

$html = array('title' => 'Admin', 'html' => $html);
template::display('generic.tmpl', $html);

?>
