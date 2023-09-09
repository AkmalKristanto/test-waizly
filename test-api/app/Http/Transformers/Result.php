<?php

namespace App\Http\Transformers;

use Illuminate\Support\Facades\Log;

/**
 *  Class Json is transformers from raw data to json view
 */
class Result
{
   public static function response($data = null, $message = null,  $code = 200, $status = true, $paginate = false)
   {
      $result = [
         'status' => $status,
         'message' => $message,
         'data' => $data,
      ];

      if ($paginate) {
         $paginationLinks = [
            'first' => $data->url(1),
            'last' => $data->url($data->lastPage()),
            'prev' => $data->previousPageUrl(),
            'next' => $data->nextPageUrl(),
         ];

         $paginationMeta = [
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'path' => url()->current(),
         ];

         $result['links'] = $paginationLinks;
         $result['meta'] = $paginationMeta;
      }

      return response()->json($result, $code);
   }

   public static function error($data = null, $message = null,  $code = 400, $status = false)
   {
      $result = [
         'status' => $status,
         'message' => $message,
         'data' => [],
      ];

      $user = auth()->user();
      if ($user) {
         $dataUser = [
            'id' => $user->id_user,
            'email' => $user->email,
            'username' => $user->user ? $user->user->username : null
         ];
         Log::info($dataUser);
      }
      if ($data) {
         Log::error($data);
      }

      return response()->json($result, $code);
   }

   public static function exception($data = null, $message = null, $code = 500, $status = true, $error = null)
   {
      $result['status'] = $status;
      $result['message'] = $message;
      if ($error instanceof \Exception) {
         $result['data']['error']['message'] = $error->getMessage();
         $result['data']['error']['file'] = $error->getFile();
         $result['data']['error']['line'] = $error->getLine();
      } else {
         $result['data']['error'] = $message;
      }
      return response()->json($result, $code);
   }
}
