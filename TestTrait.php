<?php
/**
 * @author basic-app <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Test;

use ReflectionClass;
use Config\App as AppConfig;
use BasicApp\Storage\Config\Storage as StorageConfig;
use BasicApp\Uploaded\Config\Uploaded as UploadedConfig;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\HTTP\Files\UploadedFile;
use Webmozart\Assert\InvalidArgumentException;
use CodeIgniter\Security\Exceptions\SecurityException;
use App\Models\MailerLog as MailerLogModel;

trait TestTrait
{

    public function uploadFile(string $source, string $name = null)
    {
        if (!$name)
        {
            $name = basename($source);
        }

        $config = config(UploadedConfig::class);

        $this->assertNotEmpty($config);

        $storage = service('uploaded');

        $this->assertNotEmpty($storage);

        $storage->writeFile($name, $source);

        $filename = $storage->path($name);

        return [
            'name' => basename($filename),
            'type' => mime_content_type($filename),
            'tmp_name' => $filename,
            'error' => 0,
            'size' => filesize($filename)
        ];
    }

    public function storageFile(string $source, string $name = null)
    {
        if (!$name)
        {
            $name = basename($source);
        }

        $config = config(StorageConfig::class);

        $this->assertNotEmpty($config);

        $storage = service('storage');

        $this->assertNotEmpty($storage);

        $storage->writeFile($name, $source);

        $filename = $storage->path($name);

        return [
            'name' => basename($filename),
            'type' => mime_content_type($filename),
            'tmp_name' => $filename,
            'error' => 0,
            'size' => filesize($filename)
        ];
    }

    public function withJSON($body)
    {
        $this->withBody(json_encode($body));

        $this->request->setBody($this->body);

        return $this;
    }

    public function withFILES(array $files/*, bool $append = false*/)
    {
        /*
        $request = $this->request;

        if (!$request || !$append)
        {
            $request = new IncomingRequest(
                new AppConfig,
                new URI($this->appConfig->baseURL ?? 'http://example.com/'),
                $this->body,
                new UserAgent
            );            
        }
        */

        $collection = new FileCollection;

        $collection->populateFromArray($files);

        $reflection = new ReflectionClass($this->request);
        $property = $reflection->getProperty('files');
        $property->setAccessible(true);
        $property->setValue($this->request, $collection);

        return $this->withRequest($request);
    }

    public function withPOST(array $data/*, bool $append = false*/)
    {
        $this->request->setGlobal('post', $data);

        $_POST = $data;

        return $this;
    }

    public function withGET(array $data)
    {
        $this->request->setGlobal('get', $data);

        $_GET = $data;

        return $this;
    }

    public function getJSON(\CodeIgniter\Test\TestResponse $result)
    {
        $json = $result->response()->getJSON();

        $this->assertNotEmpty($json);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR); // php 7.3
    }

    public function assertStatusCode(int $code, \CodeIgniter\Test\TestResponse $testResponse)
    {
        $this->assertEquals($code, $testResponse->response()->getStatusCode());
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

    public function assertStatusValidationError(array $data)
    {
        $this->assertArrayHasKey('status', $data);

        $this->assertEquals('VALIDATION_ERROR', $data['status']);
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

    public function assertStatusDeleted(array $data)
    {
        $this->assertArrayHasKey('status', $data);

        $this->assertEquals('DELETED', $data['status']);
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

    public function assertValidationError(array $data, string $error, ?string $field = null)
    {
        $this->assertArrayHasKey('validationErrors', $data);

        if ($field)
        {
            $this->assertArrayHasKey($field, $data['validationErrors']);

            $this->assertEquals($error, $data['validationErrors'][$field]);
        }
        else
        {
            $this->assertContains($error, $data['validationErrors']);
        }
    }

    public function assertError($data, $error)
    {
        $this->assertArrayHasKey('error', $data);

        $this->assertEquals($data['error'], $error);
    }

    public function expectInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
    }

    public function expectSecurityException()
    {
        $this->expectException(SecurityException::class);
    }

    public function assertArrayValues(array $values, array $array, $key = null)
    {
        if ($key)
        {
            $this->assertArrayHasKey($key, $array);

            $array = $array[$key];
        }

        foreach($values as $k => $v)
        {
            $this->assertArrayHasKey($k, $array);

            $this->assertEquals($v, $array[$k]);
        }
    }

    // TEMPORARY HERE

    public function assertMailerLog($subject = null, $to = null)
    {
        $model = MailerLogModel::model();

        if (is_array($subject))
        {
            $message = implode('; ', $subject);

            $model->where($subject);
        }
        else
        {
            $message = '';

            if ($subject)
            {
                $model->subject($subject);
            
                $message = $subject;
            }

            if ($to)
            {
                $model->to($to);

                if ($message)
                {
                    $message .= '; To: ';
                }

                $message .= $to; 
            }
        }

        $count = $model->countAllResults();

        $this->assertEquals(1, $count, 'Message not found: ' . $message);
    }

}