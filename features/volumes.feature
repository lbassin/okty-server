Feature:
  In order to build project

  Scenario: It receives one container with no volume
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": []
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 0 volumes
    And the container test should have 0 volumes

  Scenario: It receives one container with one shared folder
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "shared",
              "host": "./",
              "container": "/tmp/html"
            }
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
    And the container test should have 1 volumes
    And the container test should have the folder "/tmp/html" bound to the folder "./" on the host

  Scenario: It receives one container with one shared folder with empty target
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "shared",
              "host": "./",
              "container": ""
            }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be " is not a valid path inside the container"

  Scenario: It receives one container with one docker volume
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "docker",
              "name": "db-data",
              "container": "/tmp/db"
            }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 1 volumes
    And the container test should have 1 volumes
    And the container test should have the folder "/tmp/db" bound to the volume "db-data"


  Scenario: It receives one container with one docker volume with empty target
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "docker",
              "name": "db-data",
              "container": ""
            }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be " is not a valid path inside the container"

  Scenario: It receives one container with both shared and docker volume
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "shared",
              "host": "./src",
              "container": "/tmp/html"
            },
            {
              "type": "docker",
              "name": "db-data",
              "container": "/tmp/db"
            }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 200
    And the version should be greater than 3.3
    And the response should contain 1 containers
    And the response should contain 1 volumes
    And the container test should have 2 volumes
    And the container test should have the folder "/tmp/html" bound to the folder "./src" on the host
    And the container test should have the folder "/tmp/db" bound to the volume "db-data"

  Scenario: It receives one container with one docker volume with path instead of name
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "docker",
              "host": "./",
              "container": "/tmp/db"
            }
          ]
        }
      }
    ]
    """
    When I send a POST request to "/build"
    Then the HTTP code in the response should be 400
    And the error message should be " is not a valid name for a container volume"

  Scenario: It receives one container with one shared volume with host starting a letter
    Given I have the payload
    """
    [
      {
        "template": "adminer",
        "args": {
          "id": "test",
          "volumes": [
            {
              "type": "shared",
              "host": "test",
              "container": "/tmp/html"
            }
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
    And the container test should have 1 volumes
    And the container test should have the folder "/tmp/html" bound to the folder "./test" on the host

  # TODO: Same host folder used twice



