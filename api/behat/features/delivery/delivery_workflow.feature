Feature: Delivery workflow (API)
  Background:
    Given I am on tenant "tornigie.fluxion.local"
    When I login as "mo@example.test" with password "password"

  Scenario: MO creates and plans a delivery
    And I create a delivery as MO
    And I plan the delivery
    Then the response status should be 200