Feature:
  In order to build project

  Scenario: It receives one container with specific version
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "my-service",
          "version": "7.3-alpine"
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes
    And the container my-service should have the tag 7.3-alpine

  Scenario: It receives one container with specific version but image is a build
    Given I have the payload
    """
    [
      {
        "template": "test-build",
        "args": {
          "id": "my-service",
          "version": "7.3-alpine"
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes
    And the container my-service should have docker/test as build path
