<?php

/*
 * Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack_font\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Font Color Plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class FontColor extends CKEditor5PluginDefault {

  /**
   * {@inheritDoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $static_plugin_config['fontColor']['colorPicker']['format'] = 'hex';
    return $static_plugin_config;
  }

}
