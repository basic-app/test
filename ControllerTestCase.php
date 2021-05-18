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

class ControllerTestCase extends \Tests\Support\DatabaseTestCase
{

    use ControllerTestTrait;

    use TestTrait;

    public function setUp() : void
    {
        parent::setUp();

        $config = config('BasicApp\Storage\Config\Storage');

        if ($config)
        {
            helper(['file']);

            $result = delete_files(FCPATH . 'test-storage');

            Assert::true($result);

            $config->basePath = 'test-storage';
        }

        $config = config('BasicApp\Uploaded\Config\Uploaded');

        if ($config)
        {
            helper(['file']);

            $result = delete_files(FCPATH . 'test-uploaded');

            Assert::true($result);

            $config->basePath = 'test-uploaded';
        }
    }

}