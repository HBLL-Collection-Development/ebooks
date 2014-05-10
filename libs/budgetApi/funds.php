<?php
/**
  * Class to get funds data from budget api
  *
  * @author Jared Howland <budget.api@jaredhowland.com>
  * @version 2014-05-09
  * @since 2014-05-08
  *
  */

namespace budgetApi;

class funds extends budget {

  private $base_url;

  public function __construct() {
    $this->base_url = parent::BASE_API_URL . 'funds.json';
  }

  public function all() {
    return $this->get_all();
  }

  public function summary() {
    return $this->get_summary();
  }

  public function by_fund_id($fund_id) {
    return $this->get_by_fund_id($fund_id);
  }

  public function by_fund_code($fund_code) {
    return $this->get_by_fund_code($fund_code);
  }

  public function by_librarian_id($librarian_id) {
    return $this->get_by_librarian_id($librarian_id);
  }

  public function by_name($name) {
    return $this->get_by_name($name);
  }

  public function by_call_number($call_number) {
    return $this->get_by_call_number($call_number);
  }

  private function get_all() {
    return file_get_contents($this->base_url);
  }

  private function get_summary() {
    return file_get_contents($this->base_url . '?summary=true');
  }

  private function get_by_fund_id($fund_id) {
    (int) $fund_id;
    return file_get_contents($this->base_url . '?fund_id=' . $fund_id);
  }

  private function get_by_fund_code($fund_code) {
    (int) $fund_code;
    return file_get_contents($this->base_url . '?fund_code=' . $fund_code);
  }

  private function get_by_librarian_id($librarian_id) {
    (int) $librarian_id;
    return file_get_contents($this->base_url . '?librarian_id=' . $librarian_id);
  }

  private function get_by_name($name) {
    $name = urlencode($name);
    return file_get_contents($this->base_url . '?name=' . $name);
  }

  private function get_by_call_number($call_number) {
    $call_number = urlencode($call_number);
    return file_get_contents($this->base_url . '?call_number=' . $call_number);
  }
}

?>
