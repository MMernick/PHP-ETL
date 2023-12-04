<?php
namespace ETL\Services;

class TransformFieldsService {
  private \SimpleXMLElement $field;
  private ?string $fieldValue;

  public function __construct(?string &$fieldValue, \SimpleXMLElement $field) {
    $this->fieldValue = $fieldValue;
    $this->field = $field;
  }

  private function sequence(int $sequence): object {
    if((string)(strtoupper((string) $this->field['type']) ?? '') === 'SEQUENCE'){
      $this->fieldValue = (string)$sequence;
    }

    return $this;
  }

  private function int(): object {
    if(strtoupper((string) $this->field['type']) === 'INTEGER'){
      $this->fieldValue = (string)preg_replace('/[^0-9]/', '', $this->fieldValue);
    }

    return $this;
  }

  private function float(): object {
    if(strtoupper((string) $this->field['type']) === 'FLOAT'){
      $this->fieldValue = (string)str_replace('.', (string)$this->field['format'], $this->fieldValue);
    }

    return $this;
  }

  private function removeSpecialChar(): object {
    if(strtoupper((string) $this->field['type']) === 'SPECIALCHAR'){
      $this->fieldValue = (string)preg_replace('/[^a-zA-Z0-9 ]/', '', $this->fieldValue);
    }

    return $this;
  }

  private function paddedBy(): object {
    $padStyle = strtoupper((string)($this->field['padStyle'] ?? 'TAIL')) === 'HEAD' ? STR_PAD_LEFT : STR_PAD_RIGHT;
    $this->fieldValue = (string)str_pad($this->fieldValue, (int)($this->field['length'] ?? 0), ((string)($this->field['paddedBy'] ?? ' ')), $padStyle);

    return $this;
  }

  private function dateNow(): object {
    if(strtoupper((string)$this->field['type']) === 'DATETIME' && !isset($this->field['name'])){
      $this->fieldValue = (string)date((string)$this->field['format'] ?? 'dmYHis');
    }

    return $this;
  }

  private function dateFormat(): object {
    if(strtoupper((string)$this->field['type']) === 'DATETIME' && isset($this->field['name'])){
      $dateTime = \DateTime::createFromFormat('d-M-y', $this->fieldValue);
      $this->fieldValue = (string)$dateTime->format((string)$this->field['format']);
    }

    return $this;
  }

  private function sum(array $recordSetCount): object {
    if(strtoupper((string)$this->field['type']) === 'SUM' && !isset($this->field['name'])){
      $this->fieldValue = (string)count($recordSetCount);
    }

    return $this;
  }

  public function formatFields(array $recordSet, int $sequence): string{
    $this
      ->sequence($sequence)
      ->int()
      ->float()
      ->removeSpecialChar()
      ->paddedBy()
      ->dateNow()
      ->dateFormat()
      ->sum($recordSet);

    return $this->fieldValue;
  }
}