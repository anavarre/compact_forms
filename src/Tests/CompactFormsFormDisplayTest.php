<?php

namespace Drupal\compact_forms\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Compact Forms functionality on user-facing forms.
 *
 * @group compact_forms
 */
class CompactFormsFormDisplayTest extends WebTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('compact_forms');

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * {@inheritdoc}
   */
  public function setUp() {
      parent::setUp();
  }

  /**
   * Test application of Compact Forms settings to default user login form.
   */
  public function testCompactFormsDefaultUserLoginForm() {
    $form_id = 'user-login-form';

    // Load user (login) page.
    $this->drupalGet('user');
    $this->assertResponse(200, 'The User Login page is available.');

    // Assert CSS and JavaScript files, and JS settings.
    $this->verifyCssAndJavaScript($form_id);
  }

  /**
   * Test application of Compact Forms settings to user password reset form.
   */
  public function testCompactFormsAddPasswordResetForm() {
    $form_id = 'user-pass';

    // Configure compact_forms module to be applied to password reminder form.
    \Drupal::configFactory()->getEditable('compact_forms.settings')
      ->set('compact_forms_ids', $form_id)
      ->set('compact_forms_field_size', 25)
      ->save();

    // Load user/password page.
    $this->drupalGet('user/password');
    $this->assertResponse(200, 'The Request New Password page is available.');

    // Assert CSS and JavaScript files, and JS settings.
    $this->verifyCssAndJavaScript($form_id);

    // Assert that form is present and size attribute has been modified.
    $xpath = $this->xpath("//form[@id='user-pass']");
    $this->assertEqual(count($xpath), 1, format_string('The %val form exists on the page.', array('%val' => $form_id)));

    $xpath = $this->xpath("//form[@id='user-pass']//input[@id='edit-name']");
    $this->assertEqual(count($xpath), 1,
      format_string('The username field is present in the %val form.', array('%val' => $form_id)));

    $xpath = $this->xpath("//form[@id='user-pass']//input[@id='edit-name' and @size='25']");
    $this->assertEqual(count($xpath), 1, 'The username field size attribute has a value of 25.');
  }

  protected function verifyCssAndJavaScript($form_id) {
    // Assert that CSS and JavaScript files are present.
    $path_css = drupal_get_path('module', 'compact_forms') . '/css/compact_forms.theme.css';
    $xpath_query = $this->buildXPathQuery('/html/head/link[contains(@href, :path)]', array(':path' => $path_css));
    $xpath = $this->xpath($xpath_query);
    $this->assertEqual(count($xpath), 1,
      format_string('The markup contains the CSS file %val', array('%val' => $path_css)));

    $path_js = drupal_get_path('module', 'compact_forms') . '/js/compact_forms.js';
    $xpath_query = $this->buildXPathQuery('/html/body/script[contains(@src, :path)]', array(':path' => $path_js));
    $xpath = $this->xpath($xpath_query);
    $this->assertEqual(count($xpath), 1,
      format_string('The markup contains the JavaScript file %val', array('%val' => $path_js)));

    // Assert compact_forms JavaScript settings.
    $settings = $this->getDrupalSettings();
    $this->assertTrue(isset($settings['compactForms']), 'JavaScript settings for compact_forms are defined.');
    $this->assertTrue((is_array($settings['compactForms']['forms']) && (in_array($form_id, $settings['compactForms']['forms']))),
      format_string('JavaScript settings for compact_forms defines the form ID %val.', array('%val' => $form_id)));
    $this->assertTrue(is_int($settings['compactForms']['stars']),
      'JavaScript settings for compact_forms defines the stars format.');
  }
}
