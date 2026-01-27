Feature: Delivery workflow (API)
  Background:
    Given I am on tenant "tornigie.fluxion.local"
    When I login as "mo@example.test" with password "password"

  Scenario: MO creates and plans a delivery
    And I create a delivery as MO
    And I plan the delivery
    Then the response status should be 200

  Scenario: MO cannot plan a delivery twice
    And I create a delivery as MO
    And I plan the delivery
    When I plan the delivery again
    Then the response status should be 409

  Scenario: MO cannot create a delivery with invalid payload
    When I create a delivery as MO with invalid payload
    Then the response status should be 400