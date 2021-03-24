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
use CodeIgniter\Test\ControllerTester;

class ControllerTestCase extends \Tests\Support\DatabaseTestCase
{

    use ControllerTester;

    use TestTrait;

    /**
     * Runs the specified method on the controller and returns the results.
     *
     * @param string $method
     * @param array  $params
     *
     * @throws InvalidArgumentException
     *
     * @return ControllerResponse
     */
    public function execute(string $method, ...$params)
    {
        if (! method_exists($this->controller, $method) || ! is_callable([$this->controller, $method]))
        {
            throw new InvalidArgumentException('Method does not exist or is not callable in controller: ' . $method);
        }

        // The URL helper is always loaded by the system
        // so ensure it's available.
        helper('url');

        $result = (new ControllerResponse())
                ->setRequest($this->request)
                ->setResponse($this->response);

        $response = null;
        try
        {
            ob_start();

            $response = $this->controller->{$method}(...$params);
        }
        catch (Throwable $e)
        {        
            //$result->response()->setStatusCode(500); // throws an error if the code is invalid, for example 0
        
            throw $e;
        }
        finally
        {
            $output = ob_get_clean();

            // If the controller returned a response, use it
            if (isset($response) && $response instanceof Response)
            {
                $result->setResponse($response);
            }

            // check if controller returned a view rather than echoing it
            if (is_string($response))
            {
                $output = $response;
                $result->response()->setBody($output);
                $result->setBody($output);
            }
            elseif (! empty($response) && ! empty($response->getBody()))
            {
                $result->setBody($response->getBody());
            }
            else
            {
                $result->setBody('');
            }
        }


        // If not response code has been sent, assume a success
        if (empty($result->response()->getStatusCode()))
        {
            $result->response()->setStatusCode(200);
        }

        return $result;
    }

}