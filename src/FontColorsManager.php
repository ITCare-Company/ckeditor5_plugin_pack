<?php


declare(strict_types=1);

namespace Drupal\ckeditor5_font;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginElementsSubsetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Font Colors plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class FontColorsManager extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface, CKEditor5PluginElementsSubsetInterface
{

    use CKEditor5PluginConfigurableTrait;


    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration(): array
    {
        return [
            'colors' => [],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Form for choosing which heading tags are available.
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state): array
    {

        $colors = $this->configuration['colors'];


        //Fieldset grouping all the saved colors
        $form['colors'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Colors'),
            '#group' => 'saved_colors',
            '#collapsible' => false,
            '#attributes' => ['id' => 'ckeditor-ui-colors-panel'],
        ];


        //Each single saved color
        foreach ($colors as $i => $color) {

            $raw = explode(':', $color);
            $hex = $raw[0];
            $label = $raw[1] ?? null;

            $form['colors']['color-' . $i] = [
                '#type' => 'inline_template',
                '#template' => '
          <div class="color" style="background-color: '.$hex.'" data-id="color-'.$i.'">
              <div class="delete-action"></div>
              <span class="label">'.$label.'</span>
          </div>
        ',
            ];

        }

        //Add color widget
        $form['colors']['color-add'] = [
            '#type' => 'inline_template',
            '#template' => '
          <div class="color add">
              <div class="add-action"></div>
          </div>
        ',
        ];

        //System field to store JSON data
        $form['colors']['color-data'] = [
            '#type' => 'hidden',
            '#default-value' => '',
            '#attributes' => ['id' => ['colors-data-store']],
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        // Match the config schema structure at ckeditor5.plugin.ckeditor5_colors.
        $form_value = $form_state->getValue('colors');

        $form_state->setValue('colors', $this->extractColors($form_value));
    }

    /**
     * {@inheritdoc}
     */
    public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        $this->configuration['colors'] = $form_state->getValue('colors');
    }

    public function extractColors($string)
    {
        $colors = explode(",", $string);
        $colors = array_map(function ($item) {
            return trim($item);
        }, $colors);

        return array_filter($colors, function ($item) {
            $color = explode(':', $item)[0] ?? '';
            return preg_match('/^#(?:[0-9a-f]{3}){1,2}$/i', $color);
        });

    }

    /**
     * {@inheritdoc}
     *
     */
    public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array
    {
        $data = $this->configuration['colors'];
        $colors = array_map(function ($data) {
            $color = explode(':', $data);
            return [
                "color" => $color[0],
                "label" => $color[1] ?? ''
            ];
        }, $data);

        return !!$colors ? [
            'fontColor' => [
                'colors' => $colors
            ],
            'fontBackgroundColor' => [
                'colors' => $colors
            ]
        ] : [];
    }


    public function getElementsSubset(): array
    {
        return ['<p>'];
    }
}
