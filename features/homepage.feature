@homepage @smoke
Feature: Homepage

  Ensure that homepage is displayed as expected.

  @api
  Scenario: Anonymous user visits homepage
    Given I go to the homepage
    And I should be in the "<front>" path
    And the response status code should be 200
    And I should see the text "Copyright"
    Then I save screenshot

  @api @javascript
  Scenario: Anonymous user visits homepage
    Given I go to the homepage
    And I should be in the "<front>" path
    Then I save screenshot
