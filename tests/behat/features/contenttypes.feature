Feature: Every content type and testing every roles available permissions

  Ensure that each created content type has the expected message shortly after pressing 'Save'

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
