Feature:
  In order to build project

  Scenario: It receive an empty request
    Given I have the payload
    """
    []
    """
    When I send a POST request to "/build"
    Then the response should be received