<?php

declare(strict_types=1);

namespace Drupal\amd_blocks;

/**
 * @todo Add class description.
 */
final class TextTransformations {

  public function reverse($text) {
    \Drupal::logger('amd_blocks')->warning('The text was reversed');
    return strrev($text);
  }

  public function uppercase($text) {
    \Drupal::logger('amd_blocks')->warning('The text was transformed into uppercase.');
    return strtoupper($text);
  }

  public function titleCase($text) {
    \Drupal::logger('amd_blocks')->warning('The text was transformed into title case.');
    return ucfirst($text);
  }

}
