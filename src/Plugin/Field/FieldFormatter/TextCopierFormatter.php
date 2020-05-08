<?php

namespace Drupal\campaignist\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Plugin implementation of the 'text_trimmed' formatter.
 *
 * Note: This class also contains the implementations used by the
 * 'text_summary_or_trimmed' formatter.
 *
 * @see \Drupal\text\Field\Formatter\TextSummaryOrTrimmedFormatter
 *
 * @FieldFormatter(
 *   id = "text_copier",
 *   label = @Translation("Text copier"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *     "string"
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
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
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

    // $render_as_summary = function (&$element) {
    //   // Make sure any default #pre_render callbacks are set on the element,
    //   // because text_pre_render_summary() must run last.
    //   $element += \Drupal::service('element_info')->getInfo($element['#type']);
    //   // Add the #pre_render callback that renders the text into a summary.
    //   $element['#pre_render'][] = [TextTrimmedFormatter::class, 'preRenderSummary'];
    //   // Pass on the trim length to the #pre_render callback via a property.
    //   $element['#text_summary_trim_length'] = $this->getSetting('trim_length');
    // };

    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'textfield_copier',
        '#text' => [
          '#type' => 'processed_text',
          '#text' => NULL,
          '#format' => $item->format,
          '#langcode' => $item->getLangcode(),
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
      // $elements[$delta] = [
      //   '#type' => 'processed_text',
      //   '#text' => NULL,
      //   '#format' => $item->format,
      //   '#langcode' => $item->getLangcode(),
      // ];

      // if ($this->getPluginId() == 'text_summary_or_trimmed' && !empty($item->summary)) {
      //   $elements[$delta]['#text'] = $item->summary;
      // }
      // else {
      //   $elements[$delta]['#text'] = $item->value;
      //   $render_as_summary($elements[$delta]);
      // }
      $elements[$delta]['#text']['#text'] = $item->value;
    }

    return $elements;
  }

}
