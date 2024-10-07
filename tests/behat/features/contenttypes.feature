Feature: Every content type and testing every roles available permissions

  Ensure that each created content type has the expected message shortly after pressing 'Save'

  @api @p0
  Scenario: Administrator creates news content
    Given I am logged in as a user with the "Administrator" role
    And I visit "node/add/article"
    And I fill in "Title" with "News Test 1"
    And I fill in "Date" with "2024-01-25"
    And I fill in "Meta Title" with "meta test 1"
    And I fill in "Meta Description" with "meta description test 1"
    And I press "Save"
    Then the response status code should be 200
    And save screenshot
    And I should see the text "News News Test 1 has been created."

  @api @p0
  Scenario: Administrator creates Events content
    Given I am logged in as a user with the "Administrator" role
    And I visit "node/add/events"
    And I fill in "Title" with "Events Test 1"
    And I fill in "Date" with "2024-01-25"
    And I fill in "Meta Title" with "meta test 3"
    And I fill in "Meta Description" with "meta description test 3"
    And I press "Save"
    Then the response status code should be 200
    And save screenshot
    And I should see the text "Events Events Test 1 has been created."

  @api @p0
  Scenario: Administrator creates Landing Page Content
    Given I am logged in as a user with the "Administrator" role
    And I visit "node/add/landing_pages"
    And I fill in "Title" with "LandingPage Test 1"
    And I fill in "Meta Title" with "meta test 7"
    And I fill in "Meta Description" with "meta description test 7"
    And I press "Save"
    Then the response status code should be 200
    And save screenshot
    And I should see the text "Basic Pages without sidebar LandingPage Test 1 has been created."

  @api @p0
  Scenario: Administrator creates Basic Page Content
    Given I am logged in as a user with the "Administrator" role
    And I visit "node/add/page"
    And I fill in "Title" with "BasicPage Test 1"
    And I fill in "Meta Title" with "meta test 8"
    And I fill in "Meta Description" with "meta description test 8"
    And I press "Save"
    Then the response status code should be 200
    And save screenshot
    And I should see the text "Basic Page BasicPage Test 1 has been created."
