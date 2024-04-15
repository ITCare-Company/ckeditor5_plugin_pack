<?php

/*
 * Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack_highlight\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Highlight Plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class Highlight extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'options' => [],
      'use_default_markers' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {

    $form['use_default_markers'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use CKEditor5 default markers'),
      '#description' => $this->t('Default CKEditor5 markers will be available with custom added markers.'),
      '#default_value' => $this->configuration['use_default_markers'] ?? TRUE,
    ];
    $form['custom_marker_wrapper'] = [
      '#type' => 'fieldset',
      '#id' => 'custom-marker-wrapper',
    ];

    $options = $this->configuration['options'];
    if ($form_state->isRebuilding()) {
      $userInput = $form_state->getUserInput();
      $options = $userInput['editor']['settings']['plugins']['ckeditor5_plugin_pack_highlight__highlight']['custom_marker_wrapper'];
    }

    foreach ($options as $markerId => $option) {
      $form['custom_marker_wrapper'][$markerId] = [
        '#type' => 'fieldset',
        '#id' => 'marker-container',
      ];
      $form['custom_marker_wrapper'][$markerId]['title'] = [
        '#type' => 'textfield',
        '#title' => 'Marker title',
        '#maxlength' => 255,
        '#default_value' => $option['title'] ?? '',
      ];
      $form['custom_marker_wrapper'][$markerId]['color'] = [
        '#type' => 'color',
        '#title' => 'Color',
        '#default_value' => $option['color'] ?? '',
      ];
      $form['custom_marker_wrapper'][$markerId]['type'] = [
        '#type' => 'select',
        '#title' => 'Type',
        '#options' => [
          'marker' => 'Marker',
          'pen' => 'Pen',
        ],
        '#default_value' => $option['type'] ?? 'marker',
        '#ajax' => FALSE,
      ];
      $form['custom_marker_wrapper'][$markerId]['delete'] = [
        '#type' => 'submit',
        '#value' => 'Remove',
        '#name' => 'marker-' . $markerId . '-delete',
        '#button_type' => 'danger',
        '#submit' => [[$this, 'removeMarker']],
        '#ajax' => [
          'callback' => [$this, 'refreshMarkersCallback'],
          'wrapper' => 'custom-marker-wrapper',
        ],
        '#attributes' => [
          'data-marker-id' => $markerId,
        ],
      ];
    }
    $form['custom_marker_wrapper']['add_custom_marker'] = [
      '#type' => 'submit',
      '#value' => 'Add Marker',
      '#id' => 'cke5-marker-add',
      '#submit' => [[$this, 'addCustomMarker']],
      '#ajax' => [
        'callback' => [$this, 'refreshMarkersCallback'],
        'wrapper' => 'custom-marker-wrapper',
      ],
    ];
    return $form;
  }

  /**
   * Add new marker handler.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function addCustomMarker(array &$form, FormStateInterface $form_state): void {
    $userInput = $form_state->getUserInput();
    $userInput['editor']['settings']['plugins']['ckeditor5_plugin_pack_highlight__highlight']['custom_marker_wrapper'][] = [];
    $form_state->setUserInput($userInput);
    $form_state->setRebuild();
  }

  /**
   * Remove handler.
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function removeMarker(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();
    $id = $trigger['#attributes']['data-marker-id'];
    $userInput = $form_state->getUserInput();
    $plugin = $userInput['editor']['settings']['plugins']['ckeditor5_plugin_pack_highlight__highlight']['custom_marker_wrapper'];
    if (isset($plugin[$id])) {
      unset($plugin[$id]);
    }
    $userInput['editor']['settings']['plugins']['ckeditor5_plugin_pack_highlight__highlight']['custom_marker_wrapper'] = $plugin;
    $form_state->setUserInput($userInput);

    $form_state->setRebuild();
  }

  /**
   * Refresh markers wrapper callback.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function refreshMarkersCallback(array &$form, FormStateInterface $form_state): array {
    $settings_element = $form['editor']['settings']['subform']['plugins']['ckeditor5_plugin_pack_highlight__highlight'] ?? $form;
    return $settings_element['custom_marker_wrapper'] ?? $settings_element;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $values = $form_state->cleanValues()->getValues();
    $this->configuration['options'] = $values['custom_marker_wrapper'] ?? [];
    $this->configuration['use_default_markers'] = $values['use_default_markers'] ?? TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $markers = $this->configuration['options'];
    foreach ($markers as $key => &$marker) {
      $marker['model'] = 'custom' . ucfirst($marker['type']) . $key;
      $marker['class'] = 'custom-highlight-' . $marker['model'];
    }
    $useDefaultMarkers = $this->configuration['use_default_markers'];
    if (!empty($markers) && $useDefaultMarkers) {
      $defaultMarkers = $this->getDefaultMarkers();
      $markers = array_merge($markers, $defaultMarkers);
    }
    if (!empty($markers)) {
      // array_values() to make sure that we pass indexed array.
      $static_plugin_config['highlight']['options'] = array_values($markers);
    }

    return $static_plugin_config;
  }

  /**
   * Returns default values for the Highlight plugin.
   *
   * @return array
   *   Array of markers.
   */
  private function getDefaultMarkers(): array {
    return [
      [
        'model' => 'yellowMarker',
        'class' => 'marker-yellow',
        'title' => 'Yellow marker',
        'color' => 'var(--ck-highlight-marker-yellow)',
        'type' => 'marker',
      ],
      [
        'model' => 'greenMarker',
        'class' => 'marker-green',
        'title' => 'Green marker',
        'color' => 'var(--ck-highlight-marker-green)',
        'type' => 'marker',
      ],
      [
        'model' => 'pinkMarker',
        'class' => 'marker-pink',
        'title' => 'Pink marker',
        'color' => 'var(--ck-highlight-marker-pink)',
        'type' => 'marker',
      ],
      [
        'model' => 'blueMarker',
        'class' => 'marker-blue',
        'title' => 'Blue marker',
        'color' => 'var(--ck-highlight-marker-blue)',
        'type' => 'marker',
      ],
      [
        'model' => 'redPen',
        'class' => 'pen-red',
        'title' => 'Red pen',
        'color' => 'var(--ck-highlight-pen-red)',
        'type' => 'pen',
      ],
      [
        'model' => 'greenPen',
        'class' => 'pen-green',
        'title' => 'Green pen',
        'color' => 'var(--ck-highlight-pen-green)',
        'type' => 'pen',
      ],
    ];
  }

}
