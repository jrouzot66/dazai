Feature: Warehouse workflow (API)
  Background:
    Given I am on tenant "tornigie.fluxion.local"
    When I login as "mo@example.test" with password "password"

  Scenario: MO creates a warehouse and sees it in the list
    When I create a warehouse as MO
    Then the response status should be 201
    When I list warehouses as MO
    Then the response status should be 200