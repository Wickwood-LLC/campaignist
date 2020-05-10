<?php

namespace Drupal\campaignist\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayFormattedBase;

/**
 * Example Node Label Extra field with formatted output.
 *
 * @ExtraFieldDisplay(
 *   id = "send_mail",
 *   label = @Translation("Send mail button"),
 *   bundles = {
 *     "node.*",
 *   },
 *   visible = false
 * )
 */
class SendMail extends ExtraFieldPlusDisplayFormattedBase {

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->t('Send mail');
  }

  /**
   * {@inheritdoc}
   */
  public function getLabelDisplay() {
    return 'hidden';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(ContentEntityInterface $entity) {
    $settings = $this->getSettings();

    $entity_type = $entity->getEntityType()->id();
    $view_mode = $settings['display'];

    $recipient = \Drupal::service('renderer')->render($entity->{$settings['recipient_field']}->view($view_mode)[0]);
    $subject = \Drupal::service('renderer')->render($entity->{$settings['subject_field']}->view($view_mode)[0]);
    $body = \Drupal::service('renderer')->render($entity->{$settings['body_field']}->view($view_mode)[0]);


    $element = [
      '#theme' => 'mail_form',
      '#recipient' => $recipient,
      '#subject' => $subject,
      '#body' => $body,
      '#remove_line_breaks_in_body' => $settings['remove_line_breaks_in_body'],
      '#button_label' => $settings['button_label'],
      '#attached' => [
        'library' => [
          'campaignist/send_mail'
        ]
      ]
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();

    $form['display'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Display'),
      '#required' => TRUE,
    ];

    $form['recipient_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Recipient field'),
      '#required' => TRUE,
    ];

    $form['subject_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject field'),
      '#required' => TRUE,
    ];

    $form['body_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Body field'),
      '#required' => TRUE,
    ];

    $form['button_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Send button label'),
      '#required' => TRUE,
    ];

    $form['remove_line_breaks_in_body'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove HTML line breaks in body field'),
      '#title' => $this->t('By default Drupal adds HTML line breaks to plain text content. Reverse that process to get true plain text content for body.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultFormValues() {
    $values = parent::defaultFormValues();

    $values += [
      'display' => NULL,
      'recipient_field' => NULL,
      'subject_field' => NULL,
      'body_field' => NULL,
      'button_label' => $this->t('Send'),
      'remove_line_breaks_in_body' => FALSE,
    ];

    return $values;
  }

}
