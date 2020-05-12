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
    $cc = NULL;
    $bcc = NULL;

    $recipient = static::getFieldValueItems($entity, $settings['recipient_field'], ';');

    if (!empty($settings['cc_field'])) {
      $cc = static::getFieldValueItems($entity, $settings['cc_field'], ';');
    }

    if (!empty($settings['bcc_field'])) {
      $bcc = static::getFieldValueItems($entity, $settings['bcc_field'], ';');
    }

    $subject = static::getSingleFieldValueItem($entity, $settings['subject_field']);
    $body = static::getSingleFieldValueItem($entity, $settings['body_field']);

    $element = [
      '#theme' => 'mail_form',
      '#recipient' => $recipient,
      '#cc' => $cc,
      '#bcc' => $bcc,
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

    $form['recipient_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Recipient field'),
      '#required' => TRUE,
      '#description' => $this->t('Name of field that would be holding email addresses of recipient(s).'),
    ];

    $form['cc_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Carbon copy (CC) field'),
      '#required' => FALSE,
      '#description' => $this->t('Name of field that would be holding email addresses of recipient(s) intended to receive copy of the email.'),
    ];

    $form['bcc_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Blind carbon copy (CC) field'),
      '#required' => FALSE,
      '#description' => $this->t('Name of field that would be holding email addresses of recipient(s) intended to receive copy of the email, but without notifying other recipients.'),
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
      '#title' => $this->t('Send link label'),
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
      'recipient_field' => NULL,
      'cc_field' => NULL,
      'bcc_field' => NULL,
      'subject_field' => NULL,
      'body_field' => NULL,
      'button_label' => $this->t('Send'),
    ];

    return $values;
  }

  public static function getSingleFieldValueItem($entity, $field_name) {
    $value = NULL;
    $field_items = $entity->get($field_name)->getValue();
    if (isset($field_items[0]['value'])) {
      $value = $field_items[0]['value'];
    }
    return $value;
  }

  public static function getFieldValueItems($entity, $field_name, $glue) {
    $values = NULL;
    $field_items = $entity->get($field_name)->getValue();
    foreach ($field_items as $field_item) {
      $values[] = $field_item['value'];
    }
    return implode($glue, $values);
  }

}
