Feature:
  In order to build project

  Scenario: It receive an empty request
    Given I have the payload
    """
    []
    """
    When I send a POST request to "/build"
    Then the version should be greater than 3.3
    And the response should contain 0 containers
    And the response should contain 0 volumes