<?php

declare(strict_types=1);

namespace Drupal\amd_blocks\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides an amd hello block block.
 */
#[Block(
  id: 'amd_blocks_hello_world',
  admin_label: new TranslatableMarkup('AMD Hello World Block'),
  category: new TranslatableMarkup('Custom'),
)]
final class HelloWorldBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $user = \Drupal::service('current_user');
    $text_transformations = \Drupal::service('amd_blocks.text_transformations');
    // ksm($user->getAccountName());
    $build['content'] = [
      '#markup' => $this->t('Hello @username!', ['@username' => $text_transformations->reverse($user->getAccountName())]),
    ];
    return $build;
  }

}
