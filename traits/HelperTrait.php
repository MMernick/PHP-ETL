<?php
namespace ETL\Traits;

trait HelperTrait {
  static function _toNumber(?string $value): ?string {
    return preg_replace('/[^a-zA-Z0-9 ]/', '', $value);
  }

  static function _reduceData(Array $sourceData, Array $keys): ?Array {
    return array_values(array_reduce($sourceData, function ($carry, $item) use ($keys) {
      $key = implode('-', array_map(function ($field) use ($item) {
        return $item[$field];
      }, $keys));

      if (!isset($carry[$key])) {
        $carry[$key] = array_intersect_key($item, array_flip($keys));
      }

      return $carry;
    }, []));
  }

  static function _groupBy(array $sourceData, string $key): array {
    $return = array();

    foreach($sourceData as $value) {
      $return[$value[$key]][] = $value;
    }

    return $return;
  }

  static function _EOLType(?string $type = 'WIN'): string {
    switch (strtoupper($type)) {
      case 'LINUX':
        return '\n';
        break;
      case 'WIN':
        return '\r\n';
        break;
      case 'MAC':
        return '\r';
        break;
    }
  }
  
  public function _isValidLookFor(object $objectXML) {
    $lookFor = $objectXML->attributes()->lookFor ?? null;

    if (!isset($lookFor) || !in_array($lookFor->__toString(), ['row', 'collection'])){
      return true;
    }
    return false;
  }

  public function _hasWildCard(string $string, array $sourceData = []): string {
    $string = $this->_replaceDateWildcards($string);
    $string = $this->_replaceVariableWildcards($string, $sourceData);

    return $string;
  }

  private function _replaceDateWildcards(string $string): string {
    if (preg_match_all('/{{DATE\((.*?)\)}}/', $string, $matchesDate, PREG_SET_ORDER)) {
      foreach ($matchesDate as $matchDate) {
        $replacement = date($matchDate[1]) ?? '';
        $string = str_replace($matchDate[0], $replacement, $string);
      }
    }

    return $string;
  }

  private function _replaceVariableWildcards(string $string, array $sourceData): string {
    if (preg_match_all('/{{(.*?)}}/', $string, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $variableName = $match[1];
        $replacement = preg_replace('/[^a-zA-Z0-9 ]/', '', $sourceData[$variableName]) ?? '';
        $string = str_replace($match[0], $replacement, $string);
      }
    }

    return $string;
  }
}