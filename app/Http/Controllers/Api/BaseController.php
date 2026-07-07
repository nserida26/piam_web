<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    //
        /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendResponse($result, $message)

    {

    	$response = [

            'success' => true,

            'result'    => $result,

            'message' => $message,

        ];



        return response()->json($response, 200);

    }



    /**

     * return error response.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendError($error, $errorMessages = [], $code = 404)

    {

    	$response = [

            'success' => false,

            'message' => $error,

        ];



        if(!empty($errorMessages)){

            $response['result'] = $errorMessages;

        }



        return response()->json($response, $code);

    }
}
