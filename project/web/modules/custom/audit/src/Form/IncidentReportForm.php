<?php

declare(strict_types=1);

namespace Drupal\audit\Form;

use Drupal\audit\Event\IncidentReport;
use Drupal\audit\Event\IncidentReportEvents;
use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Audit form.
 */
final class IncidentReportForm extends FormBase {
  /**
   * entity type manager service
   * 
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected EntityTypeManager $entityTypeManager;

  /**
   * The event dispatcher service
   * 
   * @var Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  public function __construct(EntityTypeManager $entityTypeManager, $eventDispatcher) {
    $this->entityTypeManager = $entityTypeManager;
    $this->eventDispatcher = $eventDispatcher;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'audit_incident_report';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['reporter_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reporter name'),
      '#description' => $this->t('Type your name here.'),
      '#required' => TRUE,
    ];

    $form['reporter_email'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => $this->t('Reporter email'),
      '#description' => $this->t('Type your email here.'),
    ];

    $form['entity'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Select the entity that was incorrectly deleted'),
      '#options' => $this->getEntities(),
    ];

    $form['report'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Detailed report'),
      '#description' => $this->t('Describe why this was an error.'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    return $form;
  }

  public function getEntities() {
    $storage = $this->entityTypeManager->getStorage('deletion_record');
    $query = $storage->getQuery();
    $query->sort('deleted', 'DESC');
    $query->accessCheck(TRUE);
    $ids = $query->execute();

    $records = $storage->loadMultiple($ids);
    $entities = [];

    foreach ($records as $key => $item) {
      $entities[$key] = $item->label->value;
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if (mb_strlen($form_state->getValue('report')) < 15) {
      $form_state->setErrorByName(
        'report',
        $this->t('The report should be at least 15 characters.'),
      );
    }
}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $reporter_name = $form_state->getValue('reporter_name');
    $reporter_email = $form_state->getValue('reporter_email');
    $entity = $form_state->getValue('entity');
    $report = $form_state->getValue('report');

    // Create instance of the IncidentReport event object.
    $event = new IncidentReport($reporter_name, $reporter_email, $entity, $report);

    // Trigger the event
    $this->eventDispatcher->dispatch($event, IncidentReportEvents::NEW_INCIDENT);

    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}