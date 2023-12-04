<?php
namespace ETL\Services;

class SequenceService {
  private $sequenceCtrl;

  public function __construct() {
    $this->sequenceCtrl = 0;
  }

  public function increment() {
    $this->sequenceCtrl++;
  }

  public function set($value) {
    $this->sequenceCtrl = $value;
  }

  public function getCurrentValue() {
    return $this->sequenceCtrl;
  }
}