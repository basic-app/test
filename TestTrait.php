<?php
/**
 * @author basic-app <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Test;

use Config\App as AppConfig;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;

trait TestTrait
{

    public function withJSON($body)
    {
        return $this->withBody(json_encode($body));
    }

    public function withPost(array $post)
    {
        $body = null;

        $request = new IncomingRequest(
            new AppConfig,
            new URI($this->appConfig->baseURL ?? 'http://example.com/'),
            $body,
            new UserAgent
        );

        $request->setGlobal('post', $post);        

        return $this->withRequest($request);
    }

    public function getJSON($result)
    {
        $json = $result->response()->getJSON();

        $this->assertNotEmpty($json);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR); // php 7.3
    }

    public function assertStatusCode($code, $result)
    {
        $this->assertEquals(400, $result->response()->getStatusCode());
    }

    public function assertOK($result)
    {
        $this->assertTrue($result->isOK());
    }

    public function assertStatusOK(array $data)
    {
        $this->assertArrayHasKey('status', $data);

        $this->assertEquals('OK', $data['status']);
    }

    public function assertStatusError(array $data)
    {
        $this->assertArrayHasKey('status', $data);

        $this->assertEquals('ERROR', $data['status']);
    }

    public function assertStatusCreated(array $data)
    {
        $this->assertArrayHasKey('status', $data);

        $this->assertEquals('CREATED', $data['status']);
    }

    public function assertStatusUpdated(array $data)
    {
        $this->assertArrayHasKey('status', $data);

        $this->assertEquals('UPDATED', $data['status']);
    }

    public function assertData(array $data, string $field)
    {
        $this->assertArrayHasKey('data', $data);

        $this->assertArrayHasKey($field, $data['data']);
    
        $args = func_get_args();

        if (count($args) > 2)
        {
            $this->assertEquals($args[2], $data['data'][$field]);
        }
    }

}