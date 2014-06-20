<?php

/**
 * @file
 * Test case for testing the Compact Forms module.
 */

namespace Drupal\compact_forms\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests for the compact_forms module.
 *
 * @ingroup compact_forms
 */
class CompactFormsTestCase extends WebTestBase {
  protected $privilegedUser;

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
      'name' => 'Compact Forms functionality',
      'description' => 'Tests the Compact Forms module.',
      'group' => 'Compact Forms',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->privilegedUser = $this->drupalCreateUser(array('administer Compact Forms',));
    $this->drupalLogin($this->privilegedUser);
  }

  /**
   * Test the compact_forms settings form.
   *
   * Test the following:
   * - We can successfully access the compact_forms settings form.
   * - An anonymous user cannot access the settings form.
   */
  public function testCompactFormsSettings() {

    // Verify if we can successfully access the compact_forms form.
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertResponse(200, 'The Compact Forms settings page is available.');
    $this->assertTitle(t('Compact Forms | Drupal'), 'The title on the page is "Compact Forms".');

    // Verify each and every field.
    // @todo Test the textarea.
    $this->assertFieldChecked('edit-compact-forms-descriptions', "The \"Hide field descriptions\" checkbox is checked");
    // @todo Debug assertOptionSelected.
    $this->assertOptionSelected('edit-compact-forms-stars', 2,"The required field marker defaults to \"Append star after the form element\".");
    // @todo Test empty form field.
    // @todo Test Saving the configuration settings.

    // Verify that there's no access bypass.
    $this->drupalLogout();
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertResponse(403, 'Access denied for anonymous user.');
  }
}
