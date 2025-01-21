Feature: Access to every content type for every role

  Ensure that content access permissions are set correctly
  for designated roles.

  @api
  Scenario Outline: Anonymous user receives the correct response code
    Given I go to "node/add/<content_type>"
    And save screenshot
    Then I should get a 404 HTTP response

    Examples:
    | page       |

  @api
  Scenario Outline: Users have access to create Page content
    Given I am logged in as a user with the "<role>" role
    When I go to "node/add/page"
    And save screenshot
    Then I should get a "<access_code>" HTTP response

    Examples:
      | role                | access_code |
      | Authenticated user  | 403         |
      | Admin               | 200         |
