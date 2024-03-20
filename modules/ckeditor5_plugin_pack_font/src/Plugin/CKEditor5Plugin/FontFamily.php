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
class FontFamily extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    if ($this->configuration['options']) {
      [$options] = $this->getParsedOptions($this->configuration['options']);
    }
    if (!empty($options)) {
      $static_plugin_config['fontFamily']['options'] = $options;
    }

    $static_plugin_config['fontFamily']['supportAllValues'] = $this->configuration['support_all_values'];

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
      '#description' => $this->t('Each option consists of one or more comma–separated font family names. The first font name is used as the dropdown item description in the UI.<br />

<b>Note:</b> The family names that consist of spaces should not have quotes (as opposed to the CSS standard). The necessary quotes will be added automatically in the view.<br />

Enter one or more values (one value = one line). Note that "default" is controlled by the default styles of the web page<br /> <br />
                <b>Example:</b><br />
               <code>
                default<br />
                Ubuntu, Arial, sans-serif<br />
                Ubuntu Mono, Courier New, Courier, monospace<br /></code>'),
      '#default_value' => $this->configuration['options'],
    ];

    $form['support_all_values'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support all values'),
      '#description' => $this->t('
      By default, the plugin removes any <code>font-family</code> value that does not match the plugin\'s configuration.<br />
      It means that if you paste content with font families that the editor does not understand,
      the font-family attribute will be removed and the content will be displayed with the default font.<br />
      You can preserve pasted font family values by selecting the checkbox'),
      '#default_value' => $this->configuration['support_all_values'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    [, $badValues] = $this->getParsedOptions($form_state->getValue('options'));
    if (!empty($badValues)) {
      $form_state->setError($form['options'], 'Unacceptable values provided for the CKEditor 5 Font Family plugin.');
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
      $regex = '/\b\w+\b,\s*\b\w+\b/';
      $options = explode("\n", $options);
      foreach ($options as $option) {
        $trimmedOption = trim($option);
        if (empty($trimmedOption)) {
          continue;
        }
        if (!preg_match($regex, $trimmedOption) && $trimmedOption !== 'default') {
          $badValues[] = $trimmedOption;
        }
        $returnOptions[] = $trimmedOption;
      }
    }
    return [$returnOptions, $badValues];
  }

}
