<?php

/*
 * Copyright (c) 2003-2025, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack_templates\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginInterface;
use Drupal\ckeditor5_plugin_pack\Utility\LibraryVersionChecker;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\EditorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CKEditor 5 Productivity Pack Content Templates Plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class Template extends CKEditor5PluginDefault implements CKEditor5PluginInterface, ContainerFactoryPluginInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * The id of the plugin in productivity pack.
   */
  const PRODUCTIVITY_PACK_PLUGIN_ID = 'template';

  /**
   * Creates the plugin instance.
   *
   * @param \Drupal\ckeditor5_plugin_pack\Utility\LibraryVersionChecker $libraryVersionChecker
   *   The CKEditor 5 library version checker.
   * @param mixed ...$parent_arguments
   *   The parent plugin arguments.
   */
  public function __construct(
    protected LibraryVersionChecker $libraryVersionChecker,
    ...$parent_arguments) {
    parent::__construct(...$parent_arguments);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('ckeditor5_plugin_pack.core_library_version_checker'),
      $configuration,
      $plugin_id,
      $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $plugin = $this->getFeaturedPluginId();

    $definitions = $this->getAvailableTemplates($editor);
    if ($definitions) {
      $static_plugin_config[$plugin]['definitions'] = $definitions;
    }

    if ($this->libraryVersionChecker->isLibraryVersionHigherOrEqual('44.0.0')) {
      $static_plugin_config['licenseKey'] = 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3NjU4NDMyMDAsImp0aSI6Ijc3ODRmMzRjLTdhYzMtNDU5ZS1iZTljLTIyODU5OTY1NWJkNiIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiXSwiZmVhdHVyZXMiOlsiRFJVUCJdLCJ2YyI6IjZlM2QxYWEwIn0.u2VxSw6ed4FjRh2kRYxBaHUUXOFOMyMMRLA89ihYRGclY9cvLSJNqVv32UKcyUbHZJfyVFBWiCiynmIMEPd99A';
    }
    else {
      $static_plugin_config['licenseKey'] = 'ZmlrcG1PM0RWMUU2Z2hvSEQ4eWViVEswTkxBRkFoWUxqUEpsS1JwT2o2YnhNREJvTmNPekE1TmRxL2VJa0I2OUVJZDM2ajF0WVIrWFRUY0tPZERYanoxdnRJUjljV3RkR0RhTHFSV3dQVkJtMzNLdjJNWWpVVmlSQ0pKcjVuUWpJaWpYNGhqTzYxZ0ZKbzIyVkNqRWQweEtMUXJxSzU2K1dFMGtSYVNVeXVja1FxNGtLZFJ5R3ZaSFd2REN1aWFaQmNFRUhzZUtTR01SV1Ira2NUR29iTmJ1Vi9IUFMxWWxHRkFtN3F6T0VLTnBqd1dNTTQ3V1lFVGUwSmI4bXBJPS1NakF5TlRFeE1UTT0=';
    }

    return $static_plugin_config;
  }

  /**
   * Gets the featured plugin id.
   *
   * @return string
   *   The CKEditor plugin name.
   */
  public function getFeaturedPluginId(): string {
    return self::PRODUCTIVITY_PACK_PLUGIN_ID;
  }

  /**
   * Returns array of CKEditor5 templates.
   *
   * @param \Drupal\editor\EditorInterface $editor
   *   Editor.
   *
   * @return array
   *   An Array of CKEditor5 templates definitions for the editor.
   */
  protected function getAvailableTemplates(EditorInterface $editor): array {
    $format = $editor->getFilterFormat()->id();

    $entityStorage = \Drupal::service('entity_type.manager')
      ->getStorage('ckeditor5_template');
    $query = $entityStorage->getQuery();
    $query->condition('status', TRUE);
    $query->condition('textFormats.*', $format, '=');
    $query->sort('weight');
    $results = $query->execute();

    $templates = $entityStorage->loadMultiple($results);
    $definitions = [];
    foreach ($templates as $template) {
      $definitions[] = $template->getDefinition();
    }

    return $definitions;
  }

}
