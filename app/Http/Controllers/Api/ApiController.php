<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;

class ApiController extends BaseController
{
    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successWhen($condition, $data = [], $status = 200, $headers = [], $options = 0)
    {
        $method = value($condition) ? 'success' : 'error';
        return $this->{$method}($data, $status, $headers, $options);
    }

    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $status = 200, $headers = [], $options = 0)
    {
        $data = isBuilder($data) ? $data->get() : $data;

        return static::json(
            [
                'data'    => $data,
                'success' => true,
            ],
            $status,
            $headers,
            $options
        );
    }

    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json($data = [], $status = 200, $headers = [], $options = 0)
    {
        $_data = data_get($data, 'data', $data);
        $_data = isBuilder($_data) ? $_data->get() : $_data;
        if( isset($data[ 'data' ]) ) {
            $data[ 'data' ] = $_data;
        } else {
            $data = $_data;
        }
        return new JsonResponse($data, $status, $headers, $options);
    }

    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($data = [], $status = 200/*422*/, $headers = [], $options = 0)
    {
        $data = isBuilder($data) ? $data->get() : $data;

        return static::json(
            [
                'data'    => $data,
                'success' => false,
            ],
            $status,
            $headers,
            $options
        );
    }
}
