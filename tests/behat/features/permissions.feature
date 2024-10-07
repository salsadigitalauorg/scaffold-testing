Feature: Access to every content type for every role

  Ensure that content access permissions are set correctly
  for designated roles.

  @api
  Scenario Outline: Anonymous user receives the correct response code
    Given I go to "node/add/<content_type>"
    And save screenshot
    Then I should get a 404 HTTP response

    Examples:
    | card       |
    | forum      |
    | news       |
    | page       |

  @api
  Scenario Outline: Users have access to create Forum content
    Given I am logged in as a user with the "<role>" role
    When I go to "node/add/forum"
    And save screenshot
    Then I should get a "<access_code>" HTTP response

    Examples:
      | role                | access_code |
      | Authenticated user  | 200         |
      | Group Member Admin  | 200         |
      | Editor              | 200         |
      | Admin               | 200         |
      | System Admin        | 200         |

  @api
  Scenario Outline: Users have access to create News content
    Given I am logged in as a user with the "<role>" role
    When I go to "node/add/news"
    And save screenshot
    Then I should get a "<access_code>" HTTP response

    Examples:
      | role                | access_code |
      | Authenticated user  | 403         |
      | Group Member Admin  | 403         |
      | Editor              | 200         |
      | Admin               | 200         |
      | System Admin        | 200         |

  @api
  Scenario Outline: Users have access to create Page content
    Given I am logged in as a user with the "<role>" role
    When I go to "node/add/page"
    And save screenshot
    Then I should get a "<access_code>" HTTP response

    Examples:
      | role                | access_code |
      | Authenticated user  | 403         |
      | Group Member Admin  | 200         |
      | Editor              | 200         |
      | Admin               | 200         |
      | System Admin        | 200         |
