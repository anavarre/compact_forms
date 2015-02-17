<?php

/**
 * @file
 * Test case for testing the Compact Forms admin settings.
 */

namespace Drupal\compact_forms\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Compact Forms admin settings.
 *
 * @group compact_forms
 */
class CompactFormsAdminSettingsTest extends WebTestBase {
  /**
   * User account with administrative permissions
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('compact_forms', 'help');

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
    // Admin user account only needs a subset of admin permissions
    $this->adminUser = $this->drupalCreateUser(array(
      'administer site configuration',
      'access administration pages',
      'administer permissions',
      'administer Compact Forms',
    ));
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test menu link and permissions.
   */
  public function testAdminPages() {

    // Verify admin link.
    $this->drupalGet('admin/config/user-interface');
    $this->assertResponse(200, 'The User interface page is available.');
    $this->assertLink('Compact Forms settings');
    $this->assertLinkByHref('admin/config/user-interface/compact_forms');

    // Verify help page.
    $this->drupalGet('admin/help');
    $this->assertResponse(200, 'The Help page is available.');
    $this->assertLink('Compact Forms');
    $this->assertLinkByHref('admin/help/compact_forms');

    // Verify compact_forms help page.
    $this->drupalGet('admin/help/compact_forms');
    $this->assertResponse(200, 'The Compact Forms help page is available.');

    // Verify that there's no access bypass.
    $this->drupalLogout();
    $this->drupalGet('admin/config/user-interface');
    $this->assertResponse(403, 'Access denied for non-admin user.');
  }
}
