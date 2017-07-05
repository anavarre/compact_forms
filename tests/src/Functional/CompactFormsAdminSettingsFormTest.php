<?php

namespace Drupal\Tests\compact_forms\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Url;

/**
 * Tests the Compact Forms admin settings form.
 *
 * @group compact_forms
 */
class CompactFormsAdminSettingsFormTest extends BrowserTestBase {
  /**
   * User account with compact_forms permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $privilegedUser;

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
    // Privileged user should only have the compact_forms permissions.
    $this->privilegedUser = $this->drupalCreateUser(['administer Compact Forms']);
    $this->drupalLogin($this->privilegedUser);
  }

  /**
   * Test the compact_forms settings form.
   */
  public function testCompactFormsSettings() {
    // Verify if we can successfully access the compact_forms form.
    $this->drupalGet(Url::fromRoute('compact_forms.admin_settings'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Compact Forms | Drupal');

    // Verify every field exists.
    $this->assertSession()->fieldExists('edit-compact-forms-ids');
    $this->assertSession()->fieldExists('edit-compact-forms-descriptions');
    $this->assertSession()->fieldExists('compact_forms_stars');
    $this->assertSession()->fieldExists('edit-compact-forms-field-size');

    // Validate default form values.
    $this->assertSession()->fieldValueEquals('edit-compact-forms-ids', 'user-login-form');
    $this->assertSession()->checkboxChecked('edit-compact-forms-descriptions');
    $this->assertSession()->checkboxNotChecked('edit-compact-forms-stars-0');
    $this->assertSession()->checkboxNotChecked('edit-compact-forms-stars-1');
    $this->assertSession()->checkboxChecked('edit-compact-forms-stars-2');

    // @todo Determine a proper test for empty string in edit-compact-forms-field-size textarea.
    $this->assertTrue('Field edit-compact-forms-field-size always passes with empty string.', 'Debug');
    $this->assertSession()->fieldValueEquals('edit-compact-forms-field-size', '');

    // Verify that there's no access bypass.
    $this->drupalLogout();
    $this->drupalGet(Url::fromRoute('compact_forms.admin_settings'));
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Test posting data to the compact_forms settings form.
   */
  public function testCompactFormsSettingsPost() {
    // Post form with new values.
    $edit = [
      'compact_forms_ids' => 'example-form-id',
      'compact_forms_descriptions' => FALSE,
      'compact_forms_stars' => 0,
      'compact_forms_field_size' => 10,
    ];
    $this->drupalPostForm('admin/config/user-interface/compact_forms', $edit, t('Save configuration'));

    // Load settings form page and test for new values.
    $this->drupalGet(Url::fromRoute('compact_forms.admin_settings'));
    $this->assertSession()->fieldValueEquals('edit-compact-forms-ids', $edit['compact_forms_ids']);
    $this->assertSession()->checkboxNotChecked('edit-compact-forms-descriptions');
    $this->assertSession()->checkboxChecked('edit-compact-forms-stars-0');
    $this->assertSession()->checkboxNotChecked('edit-compact-forms-stars-1');
    $this->assertSession()->checkboxNotChecked('edit-compact-forms-stars-2');
    $this->assertSession()->fieldValueEquals('edit-compact-forms-field-size', $edit['compact_forms_field_size']);
  }

}
