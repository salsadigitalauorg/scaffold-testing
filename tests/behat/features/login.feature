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
    Given users:
      | name      | pass   | mail          | roles              | status |
      | site-administrator | add-me | test@test.com | Site Administrator | 1      |
    When I visit "user/login"
    And I fill in "Username" with "site-administrator"
    And I fill in "Password" with "add-me"
    And I press the "Log in" button
    Then I save screenshot
    And I should not see the text "Unrecognized username or password"
    And the response status code should be 200
    And the "h1" element should contain "site-administrator"

  @api @javascript
  Scenario: Administrator user logs in
    Given users:
      | name      | pass   | mail          | roles              | status |
      | site-administrator | add-me | test@test.com | Site Administrator | 1      |
    When I visit "user/login"
    When I fill in "Username" with "site-administrator"
    And I fill in "Password" with "add-me"
    And I press the "Log in" button
    Then I save screenshot
    And I should not see the text "Unrecognized username or password"
    And the "h1" element should contain "site-administrator"

  @api @javascript
  Scenario: Administrator user logs in
    Given I am logged in as a user with the "administer site configuration, access administration pages" permissions
    When I go to "admin"
    Then I save screenshot
