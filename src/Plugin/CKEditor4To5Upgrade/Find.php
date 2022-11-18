<?php

namespace Drupal\ckeditor_find\Plugin\CKEditor4To5Upgrade;

use Drupal\ckeditor5\HTMLRestrictions;
use Drupal\filter\FilterFormatInterface;

/**
 * Provides a CKEditor4 to CKEditor5 upgrade path for the Find button.
 *
 * @CKEditor4To5Upgrade(
 *   id = "find",
 *   cke4_buttons = {
 *     "Find",
 *     "Find RTL",
 *     "Replace",
 *   },
 *   cke4_plugin_settings = {
 *   },
 *   cke5_plugin_elements_subset_configuration = {
 *   }
 * )
 *
 */
class Find extends \Drupal\Core\Plugin\PluginBase implements \Drupal\ckeditor5\Plugin\CKEditor4To5UpgradePluginInterface {

  /**
   * @inheritDoc
   */
  public function mapCKEditor4ToolbarButtonToCKEditor5ToolbarItem(string $cke4_button, HTMLRestrictions $text_format_html_restrictions): ?array {
    $map = [
      'Find' => 'findAndReplace',
      'Find RTL' => 'findAndReplace',
      'Replace' => 'findAndReplace',
    ];
    if (key_exists($cke4_button, $map)) {
      return [$map[$cke4_button]];
    }
    return NULL;
  }

  /**
   * @inheritDoc
   */
  public function mapCKEditor4SettingsToCKEditor5Configuration(string $cke4_plugin_id, array $cke4_plugin_settings): ?array {
    throw new \OutOfBoundsException();
  }

  /**
   * @inheritDoc
   */
  public function computeCKEditor5PluginSubsetConfiguration(string $cke5_plugin_id, FilterFormatInterface $text_format): ?array {
    throw new \OutOfBoundsException();
  }

}
