<?php

/*
 * Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

declare(strict_types=1);

namespace Drupal\ckeditor5_plugin_pack\Config;

use Drupal\Core\Asset\LibraryDiscoveryInterface;

class SettingsConfigHandler implements SettingsConfigHandlerInterface {

  /**
   * The library discovery service.
   *
   * @var \Drupal\Core\Asset\LibraryDiscoveryInterface
   */
  protected LibraryDiscoveryInterface $libraryDiscovery;


  /**
   * Constructs the handler.
   *
   * @param \Drupal\Core\Asset\LibraryDiscoveryInterface $library_discovery
   *   Library discovery service.
   */
  public function __construct(protected LibraryDiscoveryInterface $library_discovery) {
    $this->libraryDiscovery = $library_discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function getDllLocation(string $file_name = ''): string {
    //TODO: config for dll location?
    $base_path = $this->getDefaultDllLocation();

    $base_path = rtrim($base_path, ' /') . '/';

    $base_path = $this->replaceTokens($base_path);

    return $base_path . $file_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultDllLocation(): string {
    return 'https://cdn.ckeditor.com/ckeditor5/' . SettingsConfigHandlerInterface::DLL_PATH_VERSION_TOKEN . '/dll/';
  }

  /**
   * Gets the DLLs version.
   *
   * @return string
   *   The DLLs version.
   */
  public function getDllVersion(): string {
    $library = $this->libraryDiscovery->getLibraryByName('core', 'ckeditor5');

    return $library['version'];
  }

  /**
   * Replaces supported tokens in passed parameter path..
   *
   * @param string $path
   *   A URL with potential tokens to replace.
   */
  protected function replaceTokens(string $path): string {
    $tokens = [
      SettingsConfigHandlerInterface::DLL_PATH_VERSION_TOKEN => $this->getDllVersion(),
    ];

    foreach ($tokens as $token => $value) {
      if (!$value) {
        continue;
      }
      $path = str_replace($token, $value, $path);
    }

    return $path;
  }

}
