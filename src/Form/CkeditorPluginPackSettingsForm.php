<?php

namespace Drupal\ckeditor5_plugin_pack\Form;

use Drupal\ckeditor5_plugin_pack\Config\SettingsConfigHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CkeditorPluginPackSettingsForm. The config form for the module.
 *
 * @package Drupal\ckeditor5_plugin_pack\Form
 */
class CkeditorPluginPackSettingsForm extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ckeditor5_plugin_pack.settings',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'ckeditor5_plugin_pack_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ckeditor5_plugin_pack.settings');
    // @codingStandardsIgnoreStart
    $dll_location_description = $this->t('Specify the path to the library using the mask,
    also place the translations folder next to the dll directory.
    Example: /libraries/'
      . SettingsConfigHandlerInterface::PATH_PLUGIN_NAME_TOKEN
      . '/'
      . SettingsConfigHandlerInterface::DLL_PATH_VERSION_TOKEN
      . '/dll');
    // @codingStandardsIgnoreEnd
    $form['dll_location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Dll location'),
      '#description' => $dll_location_description,
      '#default_value' => $config->get('dll_location'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ckeditor5_plugin_pack.settings')
      ->set('dll_location', $form_state->getValue('dll_location'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
