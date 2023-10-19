<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;


class ApiTest extends TestCase
{
    protected $baseURL = 'http://localhost';
    protected $httpClient;
    /** @var MockObject */
    protected $mockDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = $this->getMockBuilder(Client::class)->getMock();

        // Create a mock for your database (if needed)
        include 'MockDatabase.php';
        $this->mockDb = $this->getMockBuilder(MockDatabase::class)->getMock();
    }

    public function testSaveUserWithValidData()
    {
        // var_dump($this->mockDb);
        $this->mockDb->method('executeQuery')->willReturn([
            'status' => 1,
            'message' => 'Record created successfully.',
            'created_id' => 123,
        ]);

        $url = $this->baseURL . '/api/user/save'; // Correct the API endpoint URL
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'mobile' => '1234567890',
        ];

        // Simulate a valid POST request to the API
        $responseData = [
            'status' => 1, // Assuming a successful insertion.
        ];

        // Create a mock response using GuzzleHttp's Response
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
        );

        // Configure the httpClient mock to return the mock response
        $this->httpClient->method('post')->willReturn($response);

        // Make a POST request to the API
        $response = $this->httpClient->post($url, [
            'json' => $userData,
        ]);

        // Assertions for a successful request
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        var_dump($responseData);
        $this->assertEquals(1, $responseData['status']);
    }



    public function testGetUserById()
    {
        $userId =83;
        $this->existingId($userId);
    }

    public function testCreateUser()
    {
        $userData = [
            'name' => 'S',
            'email' => 'test@example.com',
            'status' => 'ACTIVE',
            'mobile' => '111111111',
        ];

        $url = $this->baseURL . '/api/user/save';
        $options = [
            'json' => $userData,
        ];

        $response = $this->httpClient->post($url, $options);

        $actualResponseCode = $response->getStatusCode();
        $data = json_decode($response->getBody()->getContents(), true);


        $this->assertEquals(200, $actualResponseCode);
        $this->assertIsArray($data);
        $this->assertEquals(1, $data['status']);

        $this->assertArrayHasKey('created_id', $data);
        $this->assertGreaterThan(0, $data['created_id']);

        $createdId = (int)$data['created_id'];
        $this->checkData($createdId, $userData);
    }


    public function testUpdateUser()
    {
        $userId = 43;
        $userData = [
            'id' => $userId,
            'name' => 'UUUU',
            'email' => 'updated@example.comm',
            'status' => 'INACTIVE',
            'mobile' => '9876543210',
        ];

        $url = $this->baseURL . '/api/user/' . $userId . '/edit';
        $options = [
            'json' => $userData,
        ];

        $this->existingId($userId);

        $response = $this->httpClient->put($url, $options);

        $actualResponseCode = $response->getStatusCode();
        $data = json_decode($response->getBody()->getContents(), true);


        $this->assertEquals(200, $actualResponseCode);
        $this->assertIsArray($data);
        $this->assertEquals(1, $data['status']);

        $this->checkData($userId, $userData);

    }



    public function testDeleteUser()
    {
        
        $userId = 91;

        $this->existingId($userId);

        $urlBeforeDeletion = $this->baseURL . '/api/user/' . $userId;
        $responseBeforeDeletion = $this->httpClient->get($urlBeforeDeletion);
        $dataBeforeDeletion = json_decode($responseBeforeDeletion->getBody()->getContents(), true);

        $url = $this->baseURL . '/api/user/' . $userId . '/delete';
        $response = $this->httpClient->delete($url);
        $actualResponseCode = $response->getStatusCode();

        $this->assertEquals(200, $actualResponseCode);

        $urlAfterDeletion = $this->baseURL . '/api/user/' . $userId;
        $responseAfterDeletion = $this->httpClient->get($urlAfterDeletion);
        $dataAfterDeletion = json_decode($responseAfterDeletion->getBody()->getContents(), true);

        $this->assertNotEquals($dataBeforeDeletion, $dataAfterDeletion);
    }




    public function existingId($userId)
    {
        $url = $this->baseURL . '/api/users/' . $userId;
        $response = $this->httpClient->get($url);

        $actualResponseCode = $response->getStatusCode();
        $data = json_decode($response->getBody()->getContents(), true);

        if ($actualResponseCode === 200) {
            $this->assertIsArray($data, 'Maybe user does not exist?');
            $this->assertArrayHasKey('id', $data);
            $this->assertArrayHasKey('name', $data);
            $this->assertArrayHasKey('email', $data);

            var_dump($data);
        } else {
            $this->fail('Something is wrong');
        }
    }

    public function checkData($userId, $data)
    {
        $urlGET = $this->baseURL . '/api/users/' . $userId;
        $responseGET = $this->httpClient->get($urlGET);

        $actualResponseCodeGET = $responseGET->getStatusCode();
        $dataGET = json_decode($responseGET->getBody()->getContents(), true);

        if ($actualResponseCodeGET === 200) {
            $this->assertEquals($data['name'], $dataGET['name'], 'fail');
            $this->assertEquals($data['status'], $dataGET['status']);
            $this->assertEquals($data['email'], $dataGET['email']);
            $this->assertEquals($data['mobile'], $dataGET['mobile']);
        } else {
            $this->fail('Data does not match!');
        }
    }
}

///////////////////////////////////////

// use PHPUnit\Framework\TestCase;
// use GuzzleHttp\Client;


// class ApiTest extends TestCase
// {
//     protected $baseURL = 'http://localhost';
//     protected $httpClient;
//     protected $environment;
//     protected $db;

//     protected function setUp(): void
//     {
//         parent::setUp();
//         $this->environment = 'testin'; // Set to 'testing' for tests, 'production' for the production environment

//         if ($this->environment === 'testing') {
//             $mockPDOPath = __DIR__ . '/../MockPDO.php';
//             echo $mockPDOPath;
//             include $mockPDOPath;

//             $this->db = new \MockPDO();
//         } else {
//             include __DIR__ . '/../DbConnect.php';
//             $this->db = new \DbConnect();
//         }
//         $this->httpClient = new Client();
//     }
//     public function testCreateUser()
//     {
//         // Mock user data for the new user
//         $userData = [
//             'name' => 'John Doe',
//             'email' => 'johndoe@example.com',
//             'status' => 'ACTIVE',
//             'mobile' => '1234567890',
//         ];

//         // Define the URL for the user creation endpoint
//         $url = $this->baseURL . '/api/user/save';

//         // Create a Guzzle HTTP client and set the request options
//         $httpClient = new Client();
//         $options = [
//             'json' => $userData, // JSON-encoded user data
//         ];

//         // Send a POST request to create the user
//         $response = $httpClient->post($url, $options);

//         // Get the HTTP response code and the response body
//         $actualResponseCode = $response->getStatusCode();
//         $data = json_decode($response->getBody()->getContents(), true);

//         var_dump($data);

//         $this->assertEquals(200, $actualResponseCode); // Ensure the response code is 200
//         $this->assertIsArray($data, "AAAAA"); // Ensure the response data is an array

//         // You can optionally check the 'status' field in the response
//         $this->assertEquals(1, $data['status']); // Ensure the 'status' field is 1 (indicating success)
//     }
// }
/////////





////////
// namespace Tests;

// use PHPUnit\Framework\TestCase;
// use GuzzleHttp\Client;

// class ApiTest extends TestCase
// {
//     protected $baseURL = 'http://localhost';
//     protected $httpClient;

//     protected function setUp(): void
//     {
//         parent::setUp();
//         $this->httpClient = new Client();
//     }

//     public function testGetUserById()
//     {
//         $userId = 2; // Define the user ID you want to test
//         $url = $this->baseURL . '/api/users/' . $userId;
//         $response = $this->httpClient->get($url);

//         $actualResponseCode = $response->getStatusCode();
//         $data = json_decode($response->getBody()->getContents(), true);
//         // var_dump($data);

//         if ($actualResponseCode === 200) {
//             $this->assertIsArray($data, 'Maybe the user does not exist?');
//             $this->assertArrayHasKey('id', $data);
//             $this->assertArrayHasKey('name', $data);
//             $this->assertArrayHasKey('email', $data);

            
//         } else {
//             $this->fail('Something is wrong');
//         }
//     }
// }
///////////





// namespace Tests;

// use PHPUnit\Framework\TestCase;
// use GuzzleHttp\Client;

// class ApiTest extends TestCase
// {
//     protected $baseURL = 'http://localhost';
//     protected $httpClient;


//     protected function setUp(): void
//     {
//         parent::setUp();
//         $this->httpClient = new Client();
//     }

//     public function testGetAllUsers()
//     {
//         $url = $this->baseURL . '/api/users/';
//         $response = $this->httpClient->get($url);

//         $actualResponseCode = $response->getStatusCode();
//         $data = json_decode($response->getBody()->getContents(), true);

//         $this->assertEquals(200, $actualResponseCode);
//         $this->assertIsArray($data);

//         foreach ($data as $user) {
//             $this->assertArrayHasKey('id', $user);
//             $this->assertArrayHasKey('name', $user);
//             $this->assertArrayHasKey('email', $user);

//             $this->assertContains($user['status'], ['ACTIVE', 'INACTIVE']);
//         }
//     }

//     public function testGetUserById()
//     {
//         $userId =2;
//         $this->existingId($userId);
//     }

//     public function testCreateUser()
//     {
//         $userData = [
//             'name' => 'S',
//             'email' => 'test@example.com',
//             'status' => 'ACTIVE',
//             'mobile' => '111111111',
//         ];

//         $url = $this->baseURL . '/api/user/save';
//         $options = [
//             'json' => $userData,
//         ];

//         $response = $this->httpClient->post($url, $options);

//         $actualResponseCode = $response->getStatusCode();
//         $data = json_decode($response->getBody()->getContents(), true);


//         $this->assertEquals(200, $actualResponseCode);
//         $this->assertIsArray($data);
//         $this->assertEquals(1, $data['status']);

//         $this->assertArrayHasKey('created_id', $data);
//         $this->assertGreaterThan(0, $data['created_id']);

//         $createdId = (int)$data['created_id'];
//         $this->checkData($createdId, $userData);
//     }


//     public function testUpdateUser()
//     {
//         $userId = 43;
//         $userData = [
//             'id' => $userId,
//             'name' => 'UUUU',
//             'email' => 'updated@example.comm',
//             'status' => 'INACTIVE',
//             'mobile' => '9876543210',
//         ];

//         $url = $this->baseURL . '/api/user/' . $userId . '/edit';
//         $options = [
//             'json' => $userData,
//         ];

//         $this->existingId($userId);

//         $response = $this->httpClient->put($url, $options);

//         $actualResponseCode = $response->getStatusCode();
//         $data = json_decode($response->getBody()->getContents(), true);


//         $this->assertEquals(200, $actualResponseCode);
//         $this->assertIsArray($data);
//         $this->assertEquals(1, $data['status']);

//         $this->checkData($userId, $userData);

//     }



//     public function testDeleteUser()
//     {
        
//         $userId = 91;

//         $this->existingId($userId);

//         $urlBeforeDeletion = $this->baseURL . '/api/user/' . $userId;
//         $responseBeforeDeletion = $this->httpClient->get($urlBeforeDeletion);
//         $dataBeforeDeletion = json_decode($responseBeforeDeletion->getBody()->getContents(), true);

//         $url = $this->baseURL . '/api/user/' . $userId . '/delete';
//         $response = $this->httpClient->delete($url);
//         $actualResponseCode = $response->getStatusCode();

//         $this->assertEquals(200, $actualResponseCode);

//         $urlAfterDeletion = $this->baseURL . '/api/user/' . $userId;
//         $responseAfterDeletion = $this->httpClient->get($urlAfterDeletion);
//         $dataAfterDeletion = json_decode($responseAfterDeletion->getBody()->getContents(), true);

//         $this->assertNotEquals($dataBeforeDeletion, $dataAfterDeletion);
//     }




//     public function existingId($userId)
//     {
//         $url = $this->baseURL . '/api/users/' . $userId;
//         $response = $this->httpClient->get($url);

//         $actualResponseCode = $response->getStatusCode();
//         $data = json_decode($response->getBody()->getContents(), true);

//         if ($actualResponseCode === 200) {
//             $this->assertIsArray($data, 'Maybe user does not exist?');
//             $this->assertArrayHasKey('id', $data);
//             $this->assertArrayHasKey('name', $data);
//             $this->assertArrayHasKey('email', $data);

//             var_dump($data);
//         } else {
//             $this->fail('Something is wrong');
//         }
//     }

//     public function checkData($userId, $data)
//     {
//         $urlGET = $this->baseURL . '/api/users/' . $userId;
//         $responseGET = $this->httpClient->get($urlGET);

//         $actualResponseCodeGET = $responseGET->getStatusCode();
//         $dataGET = json_decode($responseGET->getBody()->getContents(), true);

//         if ($actualResponseCodeGET === 200) {
//             $this->assertEquals($data['name'], $dataGET['name'], 'fail');
//             $this->assertEquals($data['status'], $dataGET['status']);
//             $this->assertEquals($data['email'], $dataGET['email']);
//             $this->assertEquals($data['mobile'], $dataGET['mobile']);
//         } else {
//             $this->fail('Data does not match!');
//         }
//     }
// }
