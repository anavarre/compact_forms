<?php

namespace Drupal\compact_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Render\Element;

/**
 * Provides a trusted callback to alter forms.
 *
 * @see compact_forms_pre_render()
 */
class CompactForms implements TrustedCallbackInterface {

   /**
    * {@inheritdoc}
    */
   public static function trustedCallbacks() {
    return [
      'compact_forms_pre_render'
    ];
  }

/**
 * Helper function to recursively alter form elements.
 *
 * @todo Perform this in #after_build instead. - Or use hook_elements() to
 *   append a #process function to all supported elements.
 */
public function _compact_forms_resize_fields(&$form, $field_size, $descriptions) {
  $x=1;
  if (empty($form) || !is_array($form)) {
    return;
  }
  foreach (Element::children($form) as $key) {
    if (!isset($form[$key]['#type'])) {
      continue;
    }
    switch ($form[$key]['#type']) {
      case 'fieldset':
        $this->_compact_forms_resize_fields($form[$key], $field_size, $descriptions);
        break;

      case 'textfield':
      case 'textarea':
      case 'password':
      case 'password_confirm':
        if (!empty($field_size)) {
          $form[$key]['#size'] = $field_size;
        }
        if (!$descriptions) {
          unset($form[$key]['#description']);
        }
        break;
    }
  }
}

  /**
   * The #pre_render callback for all forms.
   */
  public static function compact_forms_pre_render($form) {
    static $css_ids, $form_ids, $loaded, $field_size, $descriptions;

    $config = \Drupal::config('compact_forms.settings');

    // Prepare CSS form ids.
    if (!isset($css_ids)) {
      $css_ids = explode("\n", $config->get('compact_forms_ids'));
      $css_ids = array_filter(array_map('trim', $css_ids));
    }
    // Prepare Form API form ids.
    if (!isset($form_ids) && !empty($css_ids)) {
      $form_ids = [];
      foreach ($css_ids as $id) {
        $form_ids[] = strtr($id, ['-' => '_']);
      }
    }
    // Prepare form alteration settings.
    if (!isset($field_size)) {
      $field_size = $config->get('compact_forms_field_size');
      $descriptions = $config->get('compact_forms_descriptions');
    }

    if (in_array($form['form_id']['#value'], $form_ids) || (isset($form['#id']) && in_array($form['#id'], $css_ids))) {
      // If the custom #compact_forms property has been programmatically set to
      // FALSE, do not process this form.
      if (isset($form['#compact_forms']) && !$form['#compact_forms']) {
        // Also remove it from the CSS IDs being added as JS settings.
        foreach ($css_ids as $key => $value) {
          if ($value == $form['#id']) {
            unset($css_ids[$key]);
          }
        }
        return;
      }

      // Load our page requisites and JavaScript settings.
      if (!isset($loaded)) {
        // Using #attached to add css, js, and js settings. JS and CSS libraries
        // are defined in compact_forms.libraries.yml as introduced by
        // https://drupal.org/node/2201089
        $form['#attached']['library'][] = 'compact_forms/compact_forms';
        $form['#attached']['drupalSettings']['compactForms'] = [
          'forms' => $css_ids,
          'stars' => (int) $config->get('compact_forms_stars'),
        ];
        $loaded = TRUE;
      }
      // Only alter the form if a custom field size is configured or form element
      // descriptions shall be hidden.
      if (!empty($field_size) || !$descriptions) {
        $this->_compact_forms_resize_fields($form, $field_size, $descriptions);
      }
    }
    return $form;
  }

}