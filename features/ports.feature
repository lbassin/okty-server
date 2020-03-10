Feature:
  In order to build project

  Scenario: It receives one container with no ports
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "my-service",
          "ports": []
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes
    And the container my-service should have 0 ports

  Scenario: It receives one container with no ports
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "my-service",
          "ports": [
            { "host": 8080, "container": 80 }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes
    And the container my-service should have 1 port
    And the container my-service should have his port 80 mapped to the 8080 of the host

  Scenario: It receives one container with no ports
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "my-service",
          "ports": [
            { "host": 8080, "container": 80 },
            { "host": 8080, "container": 443 }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be "Port 8080 can only be mapped once"

  Scenario: It receives one container with no ports
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "my-service",
          "ports": [
            { "host": 8080, "container": 80 },
            { "host": 444, "container": 443, "local_only": true },
            { "host": 21, "container": 22, "local_only": false }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the container my-service should have his port 443 mapped to the 444 of the host
    And the container my-service should have his port 22 mapped to the 21 of the host
    And the container my-service should allows local traffic only on the host port 444
    And the container my-service should allows local traffic only on the host port 8080
    And the container my-service should allows all traffic on the host port 21

# TODO check mapping of the port on host on two differents containers


