<?php

declare(strict_types=1);

namespace Drupal\download_files\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a download files block block.
 */
#[Block(
  id: 'download_files_block',
  admin_label: new TranslatableMarkup('Download Files Block'),
  category: new TranslatableMarkup('Custom'),
)]
final class DownloadFilesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'example' => $this->t('Hello world!'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['example'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Example'),
      '#default_value' => $this->configuration['example'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['example'] = $form_state->getValue('example');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $form = \Drupal::formBuilder()->getForm('Drupal\download_files\Form\DownloadFilesForm');

    $form['#title'] = $this->t('Get your files here!');

    // $build['content'] = [
    //   '#markup' => $this->t('Implementing Download Files Form!'),
    // ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    $has_access = $account->hasPermission('access download files form');
    // @todo Evaluate the access condition here.
    return AccessResult::allowedIf($has_access);
  }

}
