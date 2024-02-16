<?php

/*
 * Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

namespace Drupal\ckeditor5_plugin_pack\Config;

interface SettingsConfigHandlerInterface {

  const DLL_PATH_VERSION_TOKEN = 'VERSION_TOKEN';

  /**
   * Gets the DLLs location.
   *
   * @return string
   *   The DLLs location.
   */
  public function getDllLocation(string $file_name = ''): string;

  /**
   * Gets the default DLL location if it was not overridden in the config.
   *
   * @return string
   *   The URL of the DLL location.
   */
  public function getDefaultDllLocation(): string;

  /**
   * Gets the DLLs version.
   *
   * @return string
   *   The DLLs version.
   */
  public function getDllVersion(): string;

}
