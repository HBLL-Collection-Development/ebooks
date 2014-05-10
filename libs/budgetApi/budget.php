<?php
/**
  * Abstract class to get data from budget API
  *
  * @author Jared Howland <budget.api@jaredhowland.com>
  * @version 2014-05-09
  * @since 2014-05-08
  *
  */

namespace budgetApi;

abstract class budget {
  const BASE_API_URL = 'http://localhost:8888/budget/v1/';

  abstract function all();
  abstract function summary();
  abstract function by_fund_id($fund_id);
  abstract function by_fund_code($fund_code);
  abstract function by_librarian_id($librarian_id);
  abstract function by_name($name);
  abstract function by_call_number($call_number);

}

?>
