@login @smoke
Feature: Login

  Ensure that user can login.

  @api
  Scenario: Administrator user logs in
    Given I am logged in as a user with the "administer site configuration, access administration pages" permissions
    When I go to "admin"
    Then I save screenshot
    And the response status code should be 200

  @api
  Scenario: Administrator user logs in
    Given I visit "user/login"
    When I fill in "Username" with "test-user"
    And I fill in "Password" with "add-me"
    And I press the "Log in" button
    Then I save screenshot
    And I should not see the text "Unrecognized username or password"
    And the response status code should be 200
    And I should be in the "portal" path

  @api @javascript
  Scenario: Administrator user logs in
    Given I visit "user/login"
    When I fill in "Username" with "test-user"
    And I fill in "Password" with "add-me"
    And I press the "Log in" button
    Then I save screenshot
    And I should not see the text "Unrecognized username or password"
    And I should be in the "portal" path

  @api @javascript
  Scenario: Administrator user logs in
    Given I am logged in as a user with the "administer site configuration, access administration pages" permissions
    When I go to "admin"
    Then I save screenshot
