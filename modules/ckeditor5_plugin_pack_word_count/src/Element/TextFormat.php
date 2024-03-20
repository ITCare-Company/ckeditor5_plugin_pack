<?php

/*
 * Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack_word_count\Element;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the Text Format utility class for handling the word count processing.
 */
class TextFormat {

  /**
   * Process the text_format form element.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the form.
   * @param array $complete_form
   *   The form structure.
   *
   * @return array
   *   The element data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function process(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    $id = $element['#id'] . '-value-ck-word-count';
    $suffix = $element['value']['#suffix'] ?? '';
    $element['value']['#suffix'] = '<div id="' . $id . '"></div>' . $suffix;
    return $element;
  }

}
