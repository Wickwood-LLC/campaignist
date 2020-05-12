<?php

namespace Drupal\campaignist\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;

/**
 * Plugin implementation of the 'text_copier' formatter.
 *
 * @FieldFormatter(
 *   id = "text_copier",
 *   label = @Translation("Text copier"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *     "string",
 *     "email"
 *   },
 *   quickedit = {
 *     "editor" = "form"
 *   }
 * )
 */
class TextCopierFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'copy_button_label' => NULL,
      'copied_button_label' => NULL,
      'disable_time' => 3000,
      'single_copy_button' => FALSE,
      'glue' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $field_config = $this->fieldDefinition;
    $element['copy_button_label'] = [
      '#title' => t('Copy button label'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('copy_button_label'),
      '#description' => t('Use a custom label on copy button. If not set, it will be %label by default.', ['%label' => $this->t('Copy')]),
    ];
    $element['copied_button_label'] = [
      '#title' => t('Copied button label'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('copied_button_label'),
      '#description' => t('Use a custom label on copy button after copying action. If not set, it will be %label by default.', ['%label' => $this->t('Copied')]),
    ];
    $element['disable_time'] = [
      '#title' => t('Button disable time'),
      '#type' => 'textfield',
      '#field_suffix' => $this->t('milliseconds'),
      '#default_value' => $this->getSetting('disable_time'),
      '#description' => t('Time period for button to be disabled after copy operation. Button will be enabled back after this time.'),
    ];
    $element['single_copy_button'] = [
      '#title' => t('Use single copy button'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('single_copy_button'),
      '#description' => t('Enable it if you want to have single copy button for multi-value fields.'),
    ];
    $element['glue'] = [
      '#title' => t('Glue character or string'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('glue'),
      '#description' => t('Enter glue character or string to be used while joining multi-value fields with single copy button'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $field_config->get('field_name') . '][settings_edit_form][settings][single_copy_button]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $copy_button_label = !empty($this->getSetting('copy_button_label')) ? $this->getSetting('copy_button_label') : $this->t('Copy');
    $copied_button_label = !empty($this->getSetting('copied_button_label')) ? $this->getSetting('copied_button_label') : $this->t('Copied');
    $summary[] = t('Copy button label: @copy_button_label.', ['@copy_button_label' => $copy_button_label]);
    $summary[] = $this->t('Copied button label: @copied_button_label.', ['@copied_button_label' => $copied_button_label]);
    $summary[] = $this->t('Button disable time: @disable_time ms.', ['@disable_time' => $this->getSetting('disable_time')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $field_config = $items->getDataDefinition();

    $single_copy_button = $this->getSetting('single_copy_button');

    if (!$single_copy_button) {
      foreach ($items as $delta => $item) {
        $elements[$delta] = [
          '#type' => 'textfield_copier',
          '#values' => [
            0 => [
              '#type' => 'processed_text',
              '#text' => NULL,
              '#format' => $item->format,
              '#langcode' => $item->getLangcode(),
            ],
          ],
          '#format' => $item->format,
          '#langcode' => $item->getLangcode(),
          '#copy_button_label' => !empty($this->getSetting('copy_button_label')) ? $this->getSetting('copy_button_label') : $this->t('Copy'),
          '#copied_button_label' => !empty($this->getSetting('copied_button_label')) ? $this->getSetting('copied_button_label') : $this->t('Copied'),
          '#disable_time' => $this->getSetting('disable_time'),
          '#entity_type' => $field_config->get('entity_type'),
          '#bundle' => $field_config->get('bundle'),
          '#field_type' => $field_config->get('field_type'),
          '#field_name' => $field_config->get('field_name'),
        ];

        $elements[$delta]['#values'][0]['#text'] = $item->value;

        $attributes = new Attribute();
        $attributes->setAttribute('class', ['value']);
        if (in_array($field_config->get('field_type'), ['string', 'email'])) {
          $attributes->setAttribute('data-value', $item->value);
        }
        $elements[$delta]['#values'][0]['#attributes'] = $attributes;
      }
    }
    else {
      $values = [];
      foreach ($items as $delta => $item) {
          $value = [
          '#type' => 'processed_text',
          '#text' => $item->value,
          '#format' => $item->format,
          '#langcode' => $item->getLangcode(),
        ];

        if (in_array($field_config->get('field_type'), ['string', 'email'])) {
          $attributes = new Attribute();
          $attributes->setAttribute('class', ['value']);
          $attributes->setAttribute('data-value', $item->value);
          $value['#attributes'] = $attributes;
        }
        $values[] = $value;
      }
      $elements[0] = [
        '#type' => 'textfield_copier',
        '#values' => $values,
        '#format' => $item->format,
        '#copy_button_label' => !empty($this->getSetting('copy_button_label')) ? $this->getSetting('copy_button_label') : $this->t('Copy'),
        '#copied_button_label' => !empty($this->getSetting('copied_button_label')) ? $this->getSetting('copied_button_label') : $this->t('Copied'),
        '#disable_time' => $this->getSetting('disable_time'),
        '#entity_type' => $field_config->get('entity_type'),
        '#bundle' => $field_config->get('bundle'),
        '#field_type' => $field_config->get('field_type'),
        '#field_name' => $field_config->get('field_name'),
        '#single_copy_button' => $single_copy_button,
        '#glue' => $this->getSetting('glue'),
      ];
    }

    return $elements;
  }

}
