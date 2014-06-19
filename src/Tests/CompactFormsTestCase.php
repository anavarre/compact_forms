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
    $this->privileged_user = $this->drupalCreateUser(array('administer Compact Forms',));
    $this->drupalLogin($this->privileged_user);
  }

  /**
   * Test routes.
   *
   * Test the following:
   * - We can successfully access the compact_forms settings form.
   * - An anonymous user cannot access the settings form.
   */
  public function testCompactFormsSettings() {

    // Verify if we can successfully access the compact_forms form.
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertResponse(200, 'The Compact Forms settings page is available.');

    // Verify that there's no access bypass.
    $this->drupalLogout();
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertResponse(403, 'Access denied for anonymous user.');
  }
}
