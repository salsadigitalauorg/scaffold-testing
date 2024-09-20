@search @p1
Feature: Search API

  As a site member, I want to search for content.

  Background:
    Given I am logged in as a user with the "administrator" role
    And I create a new user with the following details:
      | username | email                | password  |
      | jdoe     | jdoe@example.com     | pass123   |
    And I add the user "jdoe" to the "Community portal" group
    And the user "jdoe" should be in the "Community portal" group
    And I visit "/user/logout"

  @api
  Scenario: Member searches for Page content
    Given page content:
      | title                    | body                           | status |
      | [TEST] Test page 1 | test content uniquestring      | 1      |
      | [TEST] Test page 2 | test content otheruniquestring | 1      |
    And I am logged in as a user with the "administer site configuration, access administration pages" permissions
    And I index "forum" "[TEST] Test page 1" for search
    And I index "forum" "[TEST] Test page 2" for search
    And I wait 5 seconds
    And I visit "/user/logout"
    And I press the "Log out" button
    And save screenshot

    And I visit "/user/login"
    And I fill in "Username" with "jdoe"
    And I fill in "Password" with "pass123"
    And I press "Log in"
    And I visit "/portal"
    And I wait 5 seconds
    And save screenshot

    When I fill in "query" with "[TEST]"
    And I press "Search"
    Then I should be in the "/search" path
    And I should see "[TEST] Test page 1" in the ".view-content" element
    And I should see "test content uniquestring" in the ".view-content" element
    And I should see "[TEST] Test page 2" in the ".view-content" element
    And I should see "test content otheruniquestring" in the ".view-content" element

    When I fill in "query" with "otheruniquestring"
    And I press "Search"
    Then I should not see "[TEST] Test page 1" in the ".view-content" element
    And I should not see "test content uniquestring" in the ".view-content" element
    And I should see "[TEST] Test page 2" in the ".view-content" element
    And I should see "test content otheruniquestring" in the ".view-content" element
