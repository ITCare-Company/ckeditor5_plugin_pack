<?php

namespace Drupal\find_replace\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;

/**
 * Defines the "find" plugin.
 *
 * @CKEditorPlugin(
 *   id = "find",
 *   label = @Translation("CKEditor Find/Replace"),
 *   module = "find_replace"
 * )
 */
class FindReplace extends CKEditorPluginBase {

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getFile().
   */
  public function getFile() {
    return drupal_get_path('module', 'find_replace') . '/js/plugins/find/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * Implements CKEditorPluginButtonsInterface::getButtons().
   */
  public function getButtons() {
    return array(
      'Find' => array(
        'label' => t('Find'),
        'image' => drupal_get_path('module', 'find_replace') . '/js/plugins/find/icons/find.png',
      ),
      'Find RTL' => array(
        'label' => t('Find RTL'),
        'image' => drupal_get_path('module', 'find_replace') . '/js/plugins/find/icons/find-rtl.png',
      ),
      'Replace' => array(
        'label' => t('Replace'),
        'image' => drupal_get_path('module', 'find_replace') . '/js/plugins/find/icons/replace.png',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array();
  }

}
