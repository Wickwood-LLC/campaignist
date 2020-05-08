<?php

namespace Drupal\campaignist\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a processed text render element.
 *
 * @RenderElement("textfield_copier")
 */
class TextfieldCopier extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'textfield_copier',
      '#text' => '',
      '#format' => NULL,
      '#filter_types_to_skip' => [],
      '#langcode' => '',
      '#pre_render' => [
      ],
    ];
  }
}
