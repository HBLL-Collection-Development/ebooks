<?php
/**
  * Admin page to add usage to database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2014-05-07
  * @since 2014-05-07
  *
  */
require_once 'config.php';

get_funds();

function get_funds() {
  // $url   = 'http://localhost:8888/budget/v1/funds.json?list=1';
  // $funds = json_decode(get_json($url), true);
  $books = get_books();
  foreach($books AS $book) {
    $book_id  = $book['id'];
    $call_num = $book['call_num'];
    $url      = 'http://localhost:8888/budget/v1/funds.json?call_number=' . $call_num;
    $fund_id  = (int) json_decode(get_json($url), true)['response'][0]['fund_id'];
    if($fund_id != '') {
      update_books_funds($book_id, $fund_id);
    }
  }
}

function update_books_funds($book_id, $fund_id) {
  // Connect to database
  $database = new db;
  $db       = $database->connect();
  $sql      = 'INSERT INTO books_funds (book_id, fund_id) VALUES (:book_id, :fund_id) ON DUPLICATE KEY UPDATE book_id = :book_id, fund_id = :fund_id';
  $query    = $db->prepare($sql);
  $query->bindParam(':book_id', $book_id);
  $query->bindParam(':fund_id', $fund_id);
  $result = $query->execute();
  $db = NULL;
  return $result;
}

function get_books() {
  // Connect to database
  $database = new db;
  $db       = $database->connect();
  $sql      = 'SELECT id, call_num FROM books WHERE call_num IS NOT NULL';
  $query    = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

/**
 * Uses CURL to grab the JSON from SIRSI
 * From http://25labs.com/alternative-for-file_get_contents-using-curl/
 *
 * @param string URL of API call
 * @return string Data returned by API call
 **/
function get_json($url) {
  $curl      = curl_init();
  $userAgent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';

  curl_setopt($curl, CURLOPT_URL, $url); //The URL to fetch. This can also be set when initializing a session with curl_init().
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 150); //The number of seconds to wait while trying to connect.
  curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
  curl_setopt($curl, CURLOPT_FAILONERROR, TRUE); //To fail silently if the HTTP code returned is greater than or equal to 400.
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //Do not verify peer’s certificate
  curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE); //Force new connection rather than using cache
  curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
  curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.

  // If not production, give verbose feedback on curl problems
  if(config::DEVELOPMENT) {
    curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
    $verbose = fopen('php://temp', 'rw+');
    curl_setopt($curl, CURLOPT_STDERR, $verbose);
  }

  // Execute query
  $contents = curl_exec($curl);

  // Report errors if they occur
  curl_error_report($curl, $verbose);

  curl_close($curl);
  return $contents;
}

/**
 * Sends CURL error message to user when needed (and only when in development)
 * From http://25labs.com/alternative-for-file_get_contents-using-curl/
 *
 * @param mixed
 *        string  If there is a result when making CURL call, will return data
 *        boolean If there is no result when making CURL call, will return FALSE
 * @return null If error exists, prints error directly to user and kills everything
 *              Otherwise, it does not return anything
 **/
function curl_error_report($curl, $verbose = null) {
  if(curl_errno($curl)) {
    if(config::DEVELOPMENT) {
      rewind($verbose);
      $verboseLog = stream_get_contents($verbose);
      echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
      $curlVersion = curl_version();
      extract(curl_getinfo($curl));
      $metrics = <<<EOD
    URL: $url
   Code: $http_code ($redirect_count redirect(s) in $redirect_time secs)
Content: $content_type Size: $download_content_length (Own: $size_download) Filetime: $filetime
   Time: $total_time Start @ $starttransfer_time (DNS: $namelookup_time Connect: $connect_time Request: $pretransfer_time)
  Speed: Down: $speed_download (avg.) Up: $speed_upload (avg.)
   Curl: v{$curlVersion['version']}
EOD;
      echo '<pre>' . $metrics . '</pre>';
    }
    echo 'cURL Error: ' . curl_errno($curl) . ': ' . curl_error($curl) . '<br/>';
    curl_close($curl);
    die();
  }
  return;
}


?>
