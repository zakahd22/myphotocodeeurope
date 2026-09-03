<?php

class Contact {
  private $type;
  private $value;

  public function __construct($type, $value) {
    $this->value = $value;
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function getValue() {
    return $this->value;
  }
}