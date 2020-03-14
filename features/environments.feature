Feature:
  In order to build project

  Scenario: It receives one container with no env
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": []
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes
    And the container test should have 0 env

  Scenario: It receives one container with one env
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": [
            { "key": "TEST", "value": "yes" }
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
    And the container test should have 1 env
    And the container test should have the value set to "yes" for the env "TEST"

  Scenario: It receives one container with one env with letter in lowercase
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": [
            { "key": "TEst", "value": "yes" }
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
    And the container test should have 1 env
    And the container test should have the value set to "yes" for the env "TEst"

  Scenario: It receives one container with one env with integer as value
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": [
            { "key": "TEST", "value": 52 }
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
    And the container test should have 1 env
    And the container test should have the value set to "52" for the env "TEST"

  Scenario: It receives one container with one env with wrong key format
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": [
            { "key": 42, "value": "test" }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be "42 is not a valid env name"

  Scenario: It receives one container with one env with empty key
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": [
            { "key": "", "value": "test" }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be " is not a valid env name"

  Scenario: It receives one container with one env with empty value
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "environments": [
            { "key": "MYSQL_PASSWORD", "value": "" }
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
    And the container test should have 1 env
    And the container test should have the value set to "" for the env "MYSQL_PASSWORD"
