Feature:
  In order to build project

  Scenario: It receives an empty request
    Given I have the payload
    """
    []
    """
    When I send a POST request to "/build"
    Then the version should be greater than 3.3
    And the response should contain 0 containers
    And the response should contain 0 volumes
    
  Scenario: Payload is not a valid json
    Given I have the payload
    """
    { test 
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400

  Scenario: It receives one container with no args
    Given I have the payload
    """
    [
      {
        "template": "test"
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 500

  Scenario: Container ID do not have the right format
    Given I have the payload
    """
    [
      {
        "template": "test",
        "args": {
          "id": "f@ke"
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be "f@ke is not a valid id"

  Scenario: It receives one container with no args
    Given I have the payload
    """
    [
      {
        "template": "test",
        "args": {
          "id": "my-service"
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes

