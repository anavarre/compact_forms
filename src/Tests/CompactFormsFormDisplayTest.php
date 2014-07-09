<?php

namespace Drupal\compact_forms\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests for the compact_forms module.
 *
 * @ingroup compact_forms
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
  public static function getInfo() {
      return array(
          'name' => 'Compact Forms form display',
          'description' => 'Tests the Compact Forms functionality on user-facing forms.',
          'group' => 'Compact Forms',
      );
  }

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
    // Load user (login) page.
    $this->drupalGet('user');
    $this->assertResponse(200, 'The Request New Password page is available.');

    // Assert that CSS and JavaScript files are present.
    $path_css = drupal_get_path('module', "compact_forms") . "/css/compact_forms.theme.css";
    $xpath_query = $this->buildXPathQuery("/html/head/link[contains(@href, :path)]", array(":path" => $path_css));
    $xpath = $this->xpath($xpath_query);
    $this->assertEqual(count($xpath), 1, "The markup contains the CSS file {$xpath_query}");

    $path_js = drupal_get_path('module', "compact_forms") . "/js/compact_forms.js";
    $xpath_query = $this->buildXPathQuery("/html/head/script[contains(@src, :path)]", array(":path" => $path_js));
    $xpath = $this->xpath($xpath_query);
    $this->assertEqual(count($xpath), 1, "The markup contains the JavaScript file {$xpath_query}");

    // Assert compact_forms JavaScript settings.
    $settings = $this->drupalGetSettings();
    $this->assertTrue(isset($settings['compactForms']), "JavaScript settings for compact_forms are defined.");
    //$this->assertTrue((is_array($settings['compactForms']['forms']) && (count($settings['compactForms']['forms']) == 1)),
    $this->assertTrue((is_array($settings['compactForms']['forms']) && (in_array("user-login-form", $settings['compactForms']['forms']))),
      "JavaScript settings for compact_forms defines the form IDs.");
    $this->assertTrue(is_int($settings['compactForms']['stars']),
      "JavaScript settings for compact_forms defines the stars format.");
  }

  /**
   * Test application of Compact Forms settings to user password reset form.
   */
  public function testCompactFormsAddPasswordResetForm() {
    // Configure compact_forms module to be applied to password reminder form.
    $config = \Drupal::config('compact_forms.settings');
    $config->set("compact_forms_ids", "user-pass");
    $config->set("compact_forms_field_size", 25);
    $config->save();

    // Load user/password page.
    $this->drupalGet('user/password');
    $this->assertResponse(200, 'The Request New Password page is available.');

    // Assert that CSS and JavaScript files are present.
    $path_css = drupal_get_path('module', "compact_forms") . "/css/compact_forms.theme.css";
    $xpath_query = $this->buildXPathQuery("/html/head/link[contains(@href, :path)]", array(":path" => $path_css));
    $xpath = $this->xpath($xpath_query);
    $this->assertEqual(count($xpath), 1, "The markup contains the CSS file {$xpath_query}");

    $path_js = drupal_get_path('module', "compact_forms") . "/js/compact_forms.js";
    $xpath_query = $this->buildXPathQuery("/html/head/script[contains(@src, :path)]", array(":path" => $path_js));
    $xpath = $this->xpath($xpath_query);
    $this->assertEqual(count($xpath), 1, "The markup contains the JavaScript file {$xpath_query}");

    // Assert compact_forms JavaScript settings.
    $settings = $this->drupalGetSettings();
    $this->assertTrue(isset($settings['compactForms']),
      "JavaScript settings for compact_forms are defined.");
    $this->assertTrue((is_array($settings['compactForms']['forms']) && (in_array("user-pass", $settings['compactForms']['forms']))),
      "JavaScript settings for compact_forms defines the form IDs.");
    $this->assertTrue(is_int($settings['compactForms']['stars']),
      "JavaScript settings for compact_forms defines the stars format.");

    // Assert that form is present and size attribute has been modified.
    $xpath = $this->xpath("//form[@id='user-pass']");
    $this->assertEqual(count($xpath), 1, "The user-pass form exists on the page.");

    $xpath = $this->xpath("//form[@id='user-pass']//input[@id='edit-name']");
    $this->assertEqual(count($xpath), 1, "The username field is present in the user-pass form.");

    $xpath = $this->xpath("//form[@id='user-pass']//input[@id='edit-name' and @size='25']");
    $this->assertEqual(count($xpath), 1, "The username field size attribute has a value of 25.");
  }
} 