Feature: Query
  Dispatching a query should find its handler

  Scenario: Dispatch Query
    When I dispatch a query
    Then I should get a response with missing field

  Scenario: Dispatch Query with Serialization Groups
    When I dispatch a query with a group
    Then I should get a response
