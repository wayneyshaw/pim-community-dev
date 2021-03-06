@javascript
Feature: Browse families
  In order to view the families that have been created
  As an administrator
  I need to be able to view a list of them

  Scenario: Successfully display all the families
    Given a "footwear" catalog configuration
    And I am logged in as "Peter"
    When I am on the families page
    Then the grid should contain 5 elements
    And I should see the columns Code, Label and Attribute as label
    And I should see families boots, sandals and sneakers
    And the rows should be sorted ascending by Code
    And I should be able to sort the rows by Code, Label and Attribute as label
    And I should be able to use the following filters:
      | filter           | operator | value | result                                      |
      | code             | contains | a     | sandals and sneakers                        |
      | label            | contains | Boo   | boots                                       |
      | attributeAsLabel |          | Name  | boots, heels, led_tvs, sandals and sneakers |

  Scenario: Successfully keep descending sorting order after refreshing the page
    Given a "footwear" catalog configuration
    And I am logged in as "Peter"
    And I am on the families page
    And I sort by "code" value descending
    When I refresh current page
    And I wait 3 seconds
    Then the rows should be sorted descending by Code

  Scenario: Successfully keep ascending sorting order after refreshing the page
    Given a "footwear" catalog configuration
    And I am logged in as "Peter"
    And I am on the families page
    And I sort by "code" value descending
    And I sort by "code" value ascending
    When I refresh current page
    And I wait 3 seconds
    Then the rows should be sorted ascending by code

  @jira https://akeneo.atlassian.net/browse/PIM-4494
  Scenario: Successfully sort families and use them for mass edit
    Given a "footwear" catalog configuration
    And the following product:
      | sku         | family   |
      | caterpillar | boots    |
      | dr-martens  | boots    |
      | tbs         | sneakers |
      | vans        | sneakers |
    And I am logged in as "Julia"
    And I am on the products page
    When I sort by "family" value ascending
    Then the rows should be sorted ascending by family
    When I select rows caterpillar and dr-martens
    And I press "Mass Edit" on the "Bulk Actions" dropdown button
    Then I should see "Mass Edit (2 products)"
