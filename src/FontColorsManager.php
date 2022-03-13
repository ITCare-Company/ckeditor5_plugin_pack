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
            '#description' => $this->t('Enter a list of comma separated color hex codes including the starting # symbol. <br/>
                Optionally you can add the color description using a colon separator. <br/>
                Example: <code>#fff:White, #000:Black, #18515E, #ccc </code> <br/>
                Leave empty to use the plugin default color configuration.
                '),
        ];


        //Each single saved color
        foreach ($colors as $i => $color) {

            $raw = explode(':', $color);
            $hex = $raw[0];
            $label = $raw[1] ?? null;

            $form['colors']['color-'.$i] = [
                '#title' => $label ?? 'Color',
                '#type' => 'details',
                '#group' => 'color-'.$i,
                '#attributes' => ['class' => ['editor-wrapper-flex']],
            ];
            $form['colors']['color-'.$i]['hex'] = [
                '#type' => 'textfield',
                '#title' => 'Hex Code',
                '#size' => 7,
                '#maxlength' => 7,
                '#placeholder' => '#18515E',
                '#default_value' => $hex,
            ];
            $form['colors']['color-'.$i]['label'] = [
                '#type' => 'textfield',
                '#title' => 'Label',
                '#size' => 15,
                '#maxlength' => 15,
                '#placeholder' => 'Optional: color label',
                '#default_value' => $label ?? '',
            ];

            $form['colors']['color-'.$i]['delete'] = [
                '#type' => 'submit',
                '#submit' => [[$this, 'delete']],
                '#name' => 'color_delete',
                '#value' => $this->t('Delete'),
                '#button_type' => 'danger',
                '#attributes' => ['class' => ['editor-element-extra-margin']],
            ];
        }

        //Form to add new colors

        $form['colors']['new-color'] = [
            '#title' => 'Add Color',
            '#type' => 'fieldset',
            '#collapsible' => false,
            '#group' => 'new-color',
            '#attributes' => ['class' => ['editor-wrapper-flex']],
        ];
        $form['colors']['new-color']['hex'] = [
            '#type' => 'textfield',
            '#title' => 'Hex Code',
            '#size' => 7,
            '#maxlength' => 7,
            '#placeholder' => '#18515E',
        ];
        $form['colors']['new-color']['label'] = [
            '#type' => 'textfield',
            '#title' => 'Label',
            '#size' => 15,
            '#maxlength' => 15,
            '#placeholder' => 'Petrol Blue',
        ];

        $form['colors']['new-color']['add'] = [
            '#type' => 'submit',
            '#submit' => [[$this, 'add']],
            '#name' => 'color_add',
            '#value' => $this->t('Add'),
            '#button_type' => 'success',
            '#attributes' => ['class' => ['editor-element-extra-margin']],
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
