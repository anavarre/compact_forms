<?php

/**
 * @file
 * Test case for testing the Compact Forms module.
 */

namespace Drupal\compact_forms\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Compact Forms admin settings form.
 *
 * @group compact_forms
 */
class CompactFormsAdminSettingsFormTest extends WebTestBase {
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
    // Privileged user should only have the compact_forms permissions
    $this->privilegedUser = $this->drupalCreateUser(array('administer Compact Forms'));
    $this->drupalLogin($this->privilegedUser);
  }

  /**
   * Test the compact_forms settings form.
   */
  public function testCompactFormsSettings() {
    // Verify if we can successfully access the compact_forms form.
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertResponse(200, 'The Compact Forms settings page is available.');
    $this->assertTitle(t('Compact Forms | Drupal'), 'The title on the page is "Compact Forms".');

    // Verify every field exists.
    $this->assertField('edit-compact-forms-ids', 'edit-compact-forms-ids field exists');
    $this->assertField('edit-compact-forms-descriptions', 'edit-compact-forms-descriptions field exists');
    $this->assertField('compact_forms_stars', 'compact_forms_stars field exists');
    $this->assertField('edit-compact-forms-field-size', 'edit-compact-forms-field-size field exists');

    // Validate default form values.
    $this->assertFieldById('edit-compact-forms-ids', 'user-login-form', 'The edit-compact-forms-ids field has the value "user-login-form".');
    $this->assertFieldChecked('edit-compact-forms-descriptions');
    $this->assertNoFieldChecked('edit-compact-forms-stars-0');
    $this->assertNoFieldChecked('edit-compact-forms-stars-1');
    $this->assertFieldChecked('edit-compact-forms-stars-2');

    // @todo Determine a proper test for empty string in edit-compact-forms-field-size textarea.
    $this->pass('Field edit-compact-forms-field-size always passes with empty string.', 'Debug');
    $this->assertFieldById('edit-compact-forms-field-size', '', 'The edit-compact-forms-field-size field is empty.');

    // Verify that there's no access bypass.
    $this->drupalLogout();
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertResponse(403, 'Access denied for anonymous user.');
  }

  /**
   * Test posting data to the compact_forms settings form.
   */
  public function testCompactFormsSettingsPost() {
    // Post form with new values.
    $edit = array(
      'compact_forms_ids' => 'example-form-id',
      'compact_forms_descriptions' => false,
      'compact_forms_stars' => 0,
      'compact_forms_field_size' => 10,
    );
    $this->drupalPostForm('admin/config/user-interface/compact_forms', $edit, t('Save configuration'));

    // Load settings form page and test for new values.
    $this->drupalGet('admin/config/user-interface/compact_forms');
    $this->assertFieldById('edit-compact-forms-ids', $edit['compact_forms_ids'],
      format_string('The edit-compact-forms-ids field has the value %val.', array('%val' => $edit['compact_forms_ids'])));
    $this->assertNoFieldChecked('edit-compact-forms-descriptions');
    $this->assertFieldChecked('edit-compact-forms-stars-0');
    $this->assertNoFieldChecked('edit-compact-forms-stars-1');
    $this->assertNoFieldChecked('edit-compact-forms-stars-2');
    $this->assertFieldById('edit-compact-forms-field-size', $edit['compact_forms_field_size'],
      format_string('The edit-compact-forms-field-size field has the value %val.', array('%val' => $edit['compact_forms_field_size'])));
  }
}
