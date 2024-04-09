<?php

/*
 * Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack_text_transformation\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Text transformation Plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class TextTransformation extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'enabled' => FALSE,
      'extra_transformations' => '',
      'groups' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Text transformation'),
      '#default_value' => $this->configuration['enabled'] ?? FALSE,
      '#attributes' => [
        'data-editor-text-transformation' => 'status',
      ],
    ];

    $form['extra_transformations'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Custom transformations.'),
      '#description' => $this->t('Add some custom transformations. Enter one or more transformations on each line in the format: from|to. Example: :+1:|👍'),
      '#default_value' => $this->configuration['extra_transformations'],
      '#ajax' => FALSE,
      '#states' => [
        'enable' => [
          ':input[data-editor-text-transformation="status"]' => ['checked' => TRUE],
        ],
        'visible' => [
          ':input[data-editor-text-transformation="status"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['groups_container'] = [
      '#type' => 'container',
      '#states' => [
        'enable' => [
          ':input[data-editor-text-transformation="status"]' => ['checked' => TRUE],
        ],
        'visible' => [
          ':input[data-editor-text-transformation="status"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['groups_container']['groups'] = [
      '#type' => 'details',
      '#title' => $this->t('Text transformation groups'),
      '#open' => TRUE,
    ];
    $defaultTransformationsGroups = $this->getDefaultTransformations();
    foreach ($defaultTransformationsGroups as $key => $transformationGroup) {
      $group = [
        '#type' => 'details',
        '#title' => ucfirst($key),
        '#open' => TRUE,
      ];
      $defaultGroupValue = !($key === 'misc');

      $group["enabled"] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable @group_name group', ['@group_name' => ucfirst($key)]),
        '#default_value' => $this->configuration['groups'][$key]['enabled'] ?? $defaultGroupValue,
        '#attributes' => [
          "data-editor-text-transformation_{$key}_enabled" => 'status',
        ],
        '#ajax' => FALSE,
      ];
      $group['line'] = [
        '#type' => 'markup',
        '#markup' => '<hr>',
      ];
      $group['transformations'] = [
        '#type' => 'container',
        '#states' => [
          'enable' => [
            ":input[data-editor-text-transformation_{$key}_enabled=\"status\"]" => ['checked' => TRUE],
          ],
          'visible' => [
            ":input[data-editor-text-transformation_{$key}_enabled=\"status\"]" => ['checked' => TRUE],
          ],
        ],
      ];
      foreach ($transformationGroup as $tkey => $transformation) {
        $group['transformations'][$tkey] = [
          '#type' => 'checkbox',
          '#title' => "<code>" . $tkey . "</code>: " . $transformation,
          '#default_value' => $this->configuration['groups'][$key]['transformations'][$tkey]['enabled'] ?? $defaultGroupValue,
          '#ajax' => FALSE,
        ];
      }
      $form['groups_container']['groups'][$key] = $group;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $extraTransformations = $form_state->getValue('extra_transformations');
    [, $wrongValues] = $this->getParsedTransformations($extraTransformations);
    if (!empty($wrongValues)) {
      $form_state->setError($form['extra_transformations'],
        $this->t('Unacceptable values provided for the extra transformations: <code>@wrong_values</code>',
        ['@wrong_values' => implode(', ', $wrongValues)]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();
    $this->configuration['enabled'] = $values['enabled'];
    foreach ($values['groups_container']['groups'] as $key => $group) {
      $transformations = [];
      foreach ($group['transformations'] as $tkey => $transformation) {
        $transformations[$tkey] = ['enabled' => $transformation];
      }
      $this->configuration['groups'][$key] = [
        'enabled' => $group['enabled'],
        'transformations' => $transformations,
      ];
    }
    $this->configuration['extra_transformations'] = $values['extra_transformations'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    if (!$this->configuration['enabled']) {
      $static_plugin_config['removePlugins'] = ['TextTransformation'];
      return $static_plugin_config;
    }

    $transformationsGroups = $this->configuration['groups'];
    $extraTransformations = $this->configuration['extra_transformations'];

    $enabledTransformations = [];
    foreach ($transformationsGroups as $groupName => $group) {
      if (!$group['enabled'] && $groupName !== 'misc') {
        continue;
      }
      $disabledTransformations = array_filter($group['transformations'], fn($t) => !$t['enabled']);
      if (empty($disabledTransformations) && $groupName !== 'misc') {
        $enabledTransformations[] = $groupName;
        continue;
      }

      $keys = array_keys(array_diff_key($group['transformations'], $disabledTransformations));
      $enabledTransformations = array_merge($enabledTransformations, $keys);
    }

    [$extraValues] = $this->getParsedTransformations($extraTransformations);
    $include = array_merge($enabledTransformations, $extraValues);

    $static_plugin_config['typing']['transformations']['include'] = $include;

    return $static_plugin_config;
  }

  /**
   * Returns array of default CKEditor5 text transformations.
   *
   * @return array
   *   Default transformations.
   */
  private function getDefaultTransformations(): array {
    return [
      'typography' => [
        'ellipsis' => 'transforms ... to …',
        'enDash' => 'transforms -- to –',
        'emDash' => 'transforms --- to —',
      ],
      'quotes' => [
        'quotesPrimary' => 'transforms "Foo bar" to “Foo bar”',
        'quotesSecondary' => 'transforms \'Foo bar\' to ‘Foo bar’',
      ],
      'symbols' => [
        'trademark' => 'transforms (tm) to ™',
        'registeredTrademark' => 'transforms (r) to ®',
        'copyright' => 'transforms (c) to ©',
      ],
      'mathematical' => [
        'oneHalf' => 'transforms 1/2 to: ½',
        'oneThird' => 'transforms 1/3 to: ⅓',
        'twoThirds' => 'transforms 2/3 to: ⅔',
        'oneFourth' => 'transforms 1/4 to: ¼',
        'threeQuarters' => 'transforms 3/4 to: ¾',
        'lessThanOrEqual' => 'transforms <= to: ≤',
        'greaterThanOrEqual' => 'transforms >= to: ≥',
        'notEqual' => 'transforms != to: ≠',
        'arrowLeft' => 'transforms <- to: ←',
        'arrowRight' => 'transforms -> to: →',
      ],
      'misc' => [
        'quotesPrimaryEnGb' => 'transforms \'Foo bar\' to ‘Foo bar’',
        'quotesSecondaryEnGb' => 'transforms "Foo bar" to “Foo bar”',
        'quotesPrimaryPl' => 'transforms "Foo bar" to „Foo bar”',
        'quotesSecondaryPl' => 'transforms \'Foo bar\' to ‚Foo bar’',
      ],
    ];
  }

  /**
   * Transform the string into an array of extra transformations .
   *
   * @param string $transformations
   *   String to be parsed.
   *
   * @return array
   *   Array of values.
   */
  private function getParsedTransformations(string $transformations): array {
    $values = explode("\n", $transformations);
    $extraValues = [];
    $wrongValues = [];
    foreach ($values as $value) {
      $trimmedValue = trim($value);
      if (empty($trimmedValue)) {
        continue;
      }
      $transformationValue = explode('|', $trimmedValue);
      if (count($transformationValue) !== 2) {
        $wrongValues[] = $trimmedValue;
      }
      else {
        $extraValues[] = ['from' => $transformationValue[0], 'to' => $transformationValue[1]];
      }
    }
    return [$extraValues, $wrongValues];
  }

}
