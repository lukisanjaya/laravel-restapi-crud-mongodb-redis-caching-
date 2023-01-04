<?php
namespace App\Helpers;

use Illuminate\Http\Response;

class ApiResponseHelper
{
   /**
    * @param mixed string
    *
    * @return
    */
    static function respondNotFound(string $message = 'Data Not Found')
    {
        $response = [
            'status'  => false,
            'message' => $message
        ];
        return response()->json($response, Response::HTTP_NOT_FOUND);
    }

   /**
    * @param $data
    *
    * @return
    */
    static function respondCreated($data)
    {
        $response = [
            'status'  => true,
            'message' => 'Successfully',
            'data'    => $data,
        ];
        return response()->json($response, Response::HTTP_CREATED);
    }

   /**
    * @param $data
    *
    * @return
    */
    static function respondSuccess($data)
    {
        $response = [
            'status'  => true,
            'message' => 'Successfully',
            'data'    => $data
        ];
        return response()->json($response, Response::HTTP_OK);
    }

   /**
    * @param $data
    *
    * @return
    */
    static function respondSuccessCollection($data)
    {
        $response = [
            'status'  => true,
            'message' => 'Successfully',
            'data'    => $data->getCollection(),
            'meta'    => [
                "current_page"  => (int) $data->currentPage(),
                "last_page"     => (int) $data->lastPage(),
                "prev_page_url" => (string) $data->previousPageUrl(),
                "next_page_url" => (string) $data->nextPageUrl(),
                "per_page"      => (int) $data->perPage(),
                "from"          => (int) $data->firstItem(),
                "to"            => (int) $data->lastItem(),
                "total"         => (int) $data->total(),
            ]
        ];

        return response()->json($response, Response::HTTP_OK);
    }

   /**
    * @param string $message
    *
    * @return
    */
    static function respondOk(string $message = 'Successfully')
    {
        $response = [
            'status'  => true,
            'message' => $message
        ];
        return response()->json($response, Response::HTTP_OK);
    }

   /**
    * @param mixed $data
    *
    * @return
    */
    static function respondOkData($data)
    {
        return response()->json($data, Response::HTTP_OK);
    }

   /**
    * @param string $message
    *
    * @return
    */
    static function respondUnAuthenticated(string $message = 'Unauthenticated')
    {
        $response = [
            'status'  => false,
            'message' => $message
        ];
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

   /**
    * @param string $message
    *
    * @return
    */
    static function respondForbidden(string $message = 'Forbidden')
    {
        $response = [
            'status'  => false,
            'message' => $message
        ];
        return response()->json($response, Response::HTTP_FORBIDDEN);
    }

   /**
    * @param string $message
    *
    * @return
    */
    static function respondError(string $message = 'Internal Server Error')
    {
        $response = [
            'status'  => false,
            'message' => $message
        ];
        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

   /**
    * @param mixed string
    *
    * @return
    */
    static function respondBadRequest(string $message = 'Bad Request')
    {
        $response = [
            'status'  => false,
            'message' => $message
        ];
        return response()->json($response, Response::HTTP_BAD_REQUEST);
    }

   /**
    *
    * @return
    */
    static function respondNoContent()
    {
        $response = [
            'status'  => false,
            'message' => 'Failed'
        ];
        return response()->json($response, Response::HTTP_NO_CONTENT);
    }
}
