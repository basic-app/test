<?php
/**
 * @author basic-app <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Test;

use Exception;
use Throwable;
use InvalidArgumentException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\Test\ControllerResponse;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Test\ControllerTestTrait;
use Webmozart\Assert\Assert;
use BasicApp\Uploaded\UploadedTestTrait;
use BasicApp\Storage\StorageTestTrait;

class ControllerTestCase extends \Tests\Support\DatabaseTestCase
{

    use UploadedTestTrait;

    use StorageTestTrait;

    use ControllerTestTrait;

    use TestTrait;

    public function setUp() : void
    {
        $this->setUpUploaded();

        $this->setUpStorage();

        parent::setUp();
    }

    public function tearDown() : void
    {
        $this->tearDownUploaded();

        $this->tearDownStorage();

        parent::tearDown();
    }

}