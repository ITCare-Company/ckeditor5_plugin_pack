<?php

/*
 * Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack_font\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Font Size Plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class FontSize extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    if ($this->configuration['options']) {
      [$options] = $this->getParsedOptions($this->configuration['options']);
    }
    if (!empty($options)) {
      $static_plugin_config['fontSize']['options'] = $options;
    }

    $static_plugin_config['fontSize']['supportAllValues'] = $this->configuration['support_all_values'];

    return $static_plugin_config;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'options' => '',
      'support_all_values' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['options'] = [
      '#title' => $this->t('Options'),
      '#type' => 'textarea',
      '#description' => $this->t('A list of sizes (in px) that will be provided in the "Font Size" dropdown. Enter one or more values. Note that "default" is controlled by the default styles of the web page.<br /><br />
            <b>Example:</b><br />
            <code>11<br />
                13<br />
                default<br />
                17</code>'),
      '#default_value' => $this->configuration['options'],
    ];

    $form['support_all_values'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support all values'),
      '#description' => $this->t('
      If you use <b><code>Limit allowed HTML tags and correct faulty HTML</code></b> filter, by default, all <code>font-size</code> values that are not specified in the font size options are stripped.<br />
      You can enable support for all font sizes by selecting the checkbox <br />
      <b>Note: This option can only be used with numerical values as font size options.</b>'),
      '#default_value' => $this->configuration['support_all_values'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    [$validValues, $badValues] = $this->getParsedOptions($form_state->getValue('options'));
    if (!empty($badValues)) {
      $form_state->setError($form['options'], 'Unacceptable values provided for the CKEditor 5 Font Size plugin.');
    }
    if (empty($validValues) && $form_state->getValue('support_all_values')) {
      $form_state->setError($form['options'], 'Support all values can be used only with list of sizes (in px).');
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['options'] = $form_state->getValue('options');
    $this->configuration['support_all_values'] = $form_state->getValue('support_all_values');
  }

  /**
   * Transform the string into an array of options values.
   *
   * @param string|null $options
   *   String to be parsed.
   *
   * @return array
   *   Array of values.
   */
  private function getParsedOptions(?string $options): array {
    $returnOptions = [];
    $badValues = [];
    if ($options) {
      $options = explode("\n", $options);
      foreach ($options as $option) {
        $trimmedOption = trim($option);
        if (empty($trimmedOption)) {
          continue;
        }
        if (!is_numeric($trimmedOption) && $trimmedOption !== 'default') {
          $badValues[] = $trimmedOption;
        }
        $returnOptions[] = $trimmedOption;
      }
    }
    return [$returnOptions, $badValues];
  }

}
