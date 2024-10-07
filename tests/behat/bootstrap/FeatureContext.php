<?php

/**
 * @file
 * Defines application features for Behat testing within a Drupal context.
 */

namespace Salsadigitalauorg\ScaffoldTesting\Tests\behat\bootstrap;

use Behat\Gherkin\Node\TableNode;
use DrevOps\BehatSteps\ContentTrait;
use DrevOps\BehatSteps\FieldTrait;
use DrevOps\BehatSteps\FileTrait;
use DrevOps\BehatSteps\PathTrait;
use DrevOps\BehatSteps\SearchApiTrait;
use DrevOps\BehatSteps\TaxonomyTrait;
use DrevOps\BehatSteps\WaitTrait;
use DrevOps\BehatSteps\WatchdogTrait;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\group\Entity\Group;
use Drupal\group\Entity\GroupType;
use Drupal\user\Entity\User;

// Ensure TableNode is included for handling table data

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends DrupalContext
{

  use ContentTrait, FieldTrait, FileTrait, PathTrait, TaxonomyTrait, SearchApiTrait, WaitTrait, WatchdogTrait;

  /**
   * Creates a new user with specified details.
   *
   * @When I create a new user with the following details:
   * @param TableNode $table Table with user details
   */
  public function iCreateANewUserWithTheFollowingDetails(TableNode $table)
  {
    $user_info = $table->getHash()[0]; // Extract user data from the table
    $existing_user = user_load_by_name($user_info['username']);
    if ($existing_user) {
      $existing_user->delete(); // Delete existing user to avoid duplication
    }

    $user = User::create([
      'name' => $user_info['username'],
      'mail' => $user_info['email'],
      'pass' => $user_info['password'],
      'status' => 1, // 1 for active, 0 for blocked
    ]);
    $user->save();

    // Set the created user as the current user in the test environment
    $this->getUserManager()->setCurrentUser($user);
  }

  /**
   * Adds a user to a specified group.
   *
   * @When I add the user :username to the :groupName group
   *
   * @param string $username Username of the user
   * @param string $groupName Name of the group
   */
  public function iAddTheUserToTheGroup($username, $groupName)
  {
    $user = user_load_by_name($username);
    $groups = \Drupal::entityTypeManager()
      ->getStorage('group')
      ->loadByProperties(['label' => $groupName]);
    $group = reset($groups); // Assumes the group exists and there's only one with that name
    $group->addMember($user); // Adds the user as a member to the group
  }

  /**
   * Checks if a user is in a specified group.
   *
   * @Then the user :username should be in the :groupName group
   *
   * @param string $username Username of the user
   * @param string $groupName Name of the group
   *
   * @throws \Exception If the user is not in the group
   */
  public function theUserShouldBeInTheGroup($username, $groupName)
  {
    $user = user_load_by_name($username);
    $groups = \Drupal::entityTypeManager()
      ->getStorage('group')
      ->loadByProperties(['label' => $groupName]);
    $group = reset($groups);
    if (!$group->getMember($user)) {
      throw new \Exception("User {$username} is not a member of the group {$groupName}");
    }
  }

  /**
   * Selects the multiple dropdown(single select/multiple select) values
   *
   * @param $table
   *    array The list of values to verify
   * @When /^I select the following <fields> with <values>$/
   */
  public function iSelectTheFollowingFieldsWithValues(TableNode $table) {
    $multiple = TRUE;
    $table = $table->getHash();
    foreach ($table as $key => $value) {
      $select = $this->getSession()
        ->getPage()
        ->findField($table[$key]['fields']);
      if (empty($select)) {
        throw new \Exception("The page does not have the " . $table[$key]['fields'] . " field");
      }
      // The default true value for 'multiple' throws an error 'value cannot be an array' for single select fields
      $multiple = $select->getAttribute('multiple') ? TRUE : FALSE;
      $this->getSession()
        ->getPage()
        ->selectFieldOption($table[$key]['fields'], $table[$key]['values'], $multiple);
    }
  }

}

