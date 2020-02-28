Feature:
  In order to prove that the application can handle request

  Scenario: It receives a response from the home controller
    When I send a GET request to "/"
    Then the response should be received