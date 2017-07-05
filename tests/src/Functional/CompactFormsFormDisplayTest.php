<?php

namespace Drupal\Tests\compact_forms\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Url;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Tests the Compact Forms functionality on user-facing forms.
 *
 * @group compact_forms
 */
class CompactFormsFormDisplayTest extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['compact_forms'];

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
    $this->drupalGet(Url::fromRoute('user.login'));
    $this->assertSession()->statusCodeEquals(200);

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
    $this->drupalGet(Url::fromRoute('user.pass'));
    $this->assertSession()->statusCodeEquals(200);

    // Assert CSS and JavaScript files, and JS settings.
    $this->verifyCssAndJavaScript($form_id);

    // Assert that form is present and size attribute has been modified.
    $xpath = $this->xpath("//form[@id='user-pass']");
    $this->assertEquals(count($xpath), 1, new FormattableMarkup('The %val form exists on the page.', ['%val' => $form_id]));

    $xpath = $this->xpath("//form[@id='user-pass']//input[@id='edit-name']");
    $this->assertEquals(count($xpath), 1,
      new FormattableMarkup('The username field is present in the %val form.', ['%val' => $form_id]));

    $xpath = $this->xpath("//form[@id='user-pass']//input[@id='edit-name' and @size='25']");
    $this->assertEquals(count($xpath), 1, 'The username field size attribute has a value of 25.');
  }

  /**
   * Assert that CSS and JavaScript files are present.
   *
   * @param string $form_id
   *   The Form ID to retrieve CSS and JS assets for.
   */
  protected function verifyCssAndJavaScript($form_id) {
    // Assert that CSS and JavaScript files are present.
    $css_path = drupal_get_path('module', 'compact_forms') . '/css/compact_forms.theme.css';
    $xpath_query = $this->assertSession()->buildXPathQuery('/html/head/link[contains(@href, :path)]', [':path' => $css_path]);
    $xpath = $this->xpath($xpath_query);
    $this->assertEquals(count($xpath), 1,
      new FormattableMarkup('The markup contains the CSS file %val', ['%val' => $css_path]));

    $js_path = drupal_get_path('module', 'compact_forms') . '/js/compact_forms.js';
    $xpath_query = $this->assertSession()->buildXPathQuery('/html/body/script[contains(@src, :path)]', [':path' => $js_path]);
    $xpath = $this->xpath($xpath_query);
    $this->assertEquals(count($xpath), 1,
      new FormattableMarkup('The markup contains the JavaScript file %val', ['%val' => $js_path]));

    // Assert compact_forms JavaScript settings.
    $settings = $this->getDrupalSettings();
    $this->assertTrue(isset($settings['compactForms']), 'JavaScript settings for compact_forms are defined.');
    $this->assertTrue((is_array($settings['compactForms']['forms']) && (in_array($form_id, $settings['compactForms']['forms']))),
      new FormattableMarkup('JavaScript settings for compact_forms defines the form ID %val.', ['%val' => $form_id]));
    $this->assertTrue(is_int($settings['compactForms']['stars']),
      'JavaScript settings for compact_forms defines the stars format.');
  }

}
