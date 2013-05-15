<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-01
  * @since 2013-04-30
  *
  */
require_once 'config.php';

$html = <<<HTML

<h1>Normal Searching</h1><h2>Search by standard_number</h2><h3>Usage</h3><p><code>\$classify = new classify;</code></p><p><code>\$classify->search('standard_number', '0738433853');</code></p><h3>Result</h3><p><pre>Array
(
    [0] => Array
        (
            [title] => Introduction to automated data processing
            [author] => Wanous, S. J. (Samuel James), 1907-
            [oclc] => 000000059
            [call_num] => QA76
            [response_code] => 2
        )

)
</pre></p><h2>Search by title</h2><h3>Usage</h3><p><code>\$classify = new classify;</code></p><p><code>\$classify->search('title', 'Advanced Android Development: A Safari Guide');</code></p><h3>Result</h3><p><pre>Array
(
    [0] => Array
        (
            [title] => Advanced Android development a Safari guide.
            [author] => 
            [oclc] => 808125880
            [call_num] => QA76.76.A65
            [response_code] => 2
        )

)
</pre></p><h1>Bulk Searching</h1><p>Any search can be performed in bulk by passing an array instead of a string in the <code>\$terms</code> variable.</p><h2>Search by standard_number</h2><h3>Usage</h3><p><code>\$classify = new classify;</code></p><p><code>\$classify->search('standard_number', array('0738433853', '9780470053041'));</code></p><h3>Result</h3><p><pre>Array
(
    [0] => Array
        (
            [title] => Application development for CICS Web services
            [author] => 
            [oclc] => 123477218
            [call_num] => QA76.76.T45
            [response_code] => 2
        )

    [1] => Array
        (
            [title] => Applied statistics and probability for engineers
            [author] => Montgomery, Douglas C.
            [oclc] => 028632932
            [call_num] => QA276.12
            [response_code] => 2
        )

)
</pre></p><h2>Search by title</h2><h3>Usage</h3><p><code>\$classify = new classify;</code></p><p><code>\$classify->search('title', array('Advanced Android Development: A Safari Guide', 'Application development for CICS Web services'));</code></p><h3>Result</h3><p><pre>Array
(
    [0] => Array
        (
            [title] => Advanced Android development a Safari guide.
            [author] => 
            [oclc] => 808125880
            [call_num] => QA76.76.A65
            [response_code] => 2
        )

    [1] => Array
        (
            [title] => Application development for CICS Web services
            [author] => 
            [oclc] => 123477218
            [call_num] => QA76.76.T45
            [response_code] => 2
        )

)
</pre></p>
HTML;

template::display('generic.tmpl', $html);

?>