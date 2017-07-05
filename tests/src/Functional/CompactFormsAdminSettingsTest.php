<?php

namespace Drupal\Tests\compact_forms\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Url;

/**
 * Tests the Compact Forms admin settings.
 *
 * @group compact_forms
 */
class CompactFormsAdminSettingsTest extends BrowserTestBase {
  /**
   * User account with administrative permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['compact_forms', 'help'];

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
    // Admin user account only needs a subset of admin permissions.
    $this->adminUser = $this->drupalCreateUser([
      'administer site configuration',
      'access administration pages',
      'administer permissions',
      'administer Compact Forms',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test menu link and permissions.
   */
  public function testAdminPages() {

    // Verify admin link.
    $this->drupalGet(Url::fromRoute('system.admin_config_ui'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->linkExists('Compact Forms settings');
    $this->drupalGet(Url::fromRoute('compact_forms.admin_settings'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Compact Forms');

    // Verify help page.
    $this->drupalGet(Url::fromRoute('help.main'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->linkExists('Compact Forms');

    // Verify compact_forms help page.
    $this->drupalGet('admin/help/compact_forms');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Compact Forms administration pages');

    // Verify compact_forms permissions.
    $this->drupalGet(Url::fromRoute('user.admin_permissions'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Administer Compact Forms');
  }

}
