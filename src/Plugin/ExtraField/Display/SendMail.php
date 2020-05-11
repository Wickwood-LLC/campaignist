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
      '#description' => $this->t('Display (view mode) of this content type to take values for preparing the email. Recommended to create a separate display for this purpose.'),
    ];

    $form['recipient_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Recipient field'),
      '#required' => TRUE,
      '#description' => $this->t('Name of field that would be holding email addresses of recipient(s).'),
    ];

    $form['subject_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject field'),
      '#required' => TRUE,
      '#description' => $this->t('Name of field that would be holding subject line for the email.'),
    ];

    $form['body_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Plain Text Body Field'),
      '#required' => TRUE,
      '#description' => $this->t('Make sure to enter field name for a Plain, Long Text Field
      . Please do not use HTML formatted field here since "mailto" link are not supported to work with HTML formatted email content.'),
    ];

    $form['button_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Send button label'),
      '#required' => TRUE,
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
    ];

    return $values;
  }

}
