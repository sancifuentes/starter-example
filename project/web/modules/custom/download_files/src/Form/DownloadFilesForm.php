<?php

declare(strict_types=1);

namespace Drupal\download_files\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Provides a Download files form.
 */
final class DownloadFilesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'download_files_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    // $this->getMediaOptions();

    $form['media'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a file to download'),
      '#options' => $this->getMediaOptions(),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email')
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Download'),
      ],
    ];


    return $form;
  }

  // public function getMediaOptions() {
  //   // Get media items using Database Abstraction Layer

  //   $results = \Drupal::database()
  //     ->select('media_field_data', 'm')
  //     ->fields('m', ['mid', 'name'])
  //     ->condition('m.status', 1);
  //     // ->execute()
  //     // ->fetchAll();

  //   $results->leftJoin('media__field_media_file', 'mf', 'm.mid = mf.entity_id and m.vid = mf.revision_id');
  //   $results->fields('mf', ['field_media_file_target_id']);

  //   // $results->leftJoin('file_managed', 'f', 'm.mid = mf.field_media_target_id = f.fid');
  //   // $results->fields('f', ['uri']);



  //   $results = $results->execute()->fetchAll();

  //   // ksm($results);

  //   $options = [];
  //   foreach ($results as $media) {
  //     $options[$media->mid] = $media->name;
  //   }

  //   // ksm($options);

  //     return $options;
  // }

  public function getMediaOptions() {

    //Using the Entity Query -----------------------------------

    $ids = \Drupal::entityQuery('media')
      ->condition('status', 1)
      ->condition('bundle', ['image'], 'IN')
      ->accessCheck()
      ->execute();

    // ksm($ids);

    $results = Media::loadMultiple($ids);

    // ksm($results);


    $options = [];
    foreach ($results as $media) {

      $options[$media->id()] = $media->label();

      // $options[$media->field_media_image->entity->uri->value] = $media->label();
    }

    ksm($options);

      return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
    $email = $form_state->getValue('email');
    if (!strpos($email, '@evolvingweb.com')) {
      $form_state->setErrorByName('email', $this->t('Invalid email.'));
    }

    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $mid = $form_state->getValue('media');
    $media = Media::load($mid);
    // $uri = $media->field_media_image->entity->uri->value;
    // $uri = null;

    switch ($media->bundle()) {
      case 'video':
        $uri = $media->field_media_oembed_video->value;
        $this->messenger()->addStatus($this->t('The video can be found in @url.', ['@url' => $uri]));
        break;
      case 'image':
        $uri = $media->field_media_image->entity->uri->value;
        $response = new BinaryFileResponse($uri);
        $response->setContentDisposition('attachment');
        $form_state->setResponse($response);
        break;
      default:
        // Document or files
        $uri = $media->field_media_file->entity->uri->value;
        $response = new BinaryFileResponse($uri);
        $response->setContentDisposition('attachment');
        $form_state->setResponse($response);
        break;
        
    }

    // ksm($media->bundle());

    // $response = new BinaryFileResponse($uri);
    // $response->setContentDisposition('attachment');
    // $form_state->setResponse($response);
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
