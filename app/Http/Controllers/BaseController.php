<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Throwable;

class BaseController extends Controller
{
    /**
     * Status code, default to 200
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Gets the status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets a status code
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Respond with data
     *
     * @param $data
     * @return JsonResponse
     */
    public function respond($data)
    {
        return Response::json($data, $this->getStatusCode(), []);
    }

    /**
     * Respond with an error message from Exception to be used in API
     * If APP_DEBUG is set to true, then we get the full error log
     *
     * @param Throwable $throwable
     * @return JsonResponse
     */
    public function respondWithError(Throwable $throwable)
    {
        $this->setStatusCode($throwable->getCode());

        if (
            $this->statusCode > 700 ||
            $this->statusCode == 0 ||
            $this->statusCode == 42 ||
            $this->statusCode == 22 ||
            $this->statusCode == -1
        ) {
            // Likely an SQL error (for instance: foreign key dependency, not respecting default value...), let's tell them this is not authorized
            $this->setStatusCode(401);
        }

        report($throwable);

        $response = [
            'status' => 'error',
            'message' => 'There was an error running your request',
            'error' => [
                'status_code' => $this->getStatusCode()
            ],
        ];

        if (
            is_a($throwable, 'Symfony\Component\HttpKernel\Exception\HttpException') &&
            array_key_exists('explicit_message', $throwable->getHeaders()) && $throwable->getHeaders()['explicit_message']
        )
            $response['message'] = $throwable->getMessage();

        if (env('APP_DEBUG')) {
            $response = array_merge($response, [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString()
            ]);
        }

        return $this->respond($response);
    }

    /**
     * Respond with error message from Exception
     * @param $e
     * @return JsonResponse
     */
    public function respondWithErrorMessage($e)
    {
        return $this->setStatusCode($e->getCode())->respond([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
}
