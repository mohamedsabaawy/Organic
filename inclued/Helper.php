<?php
namespace included;
use Illuminate\Support\Facades\Validator;

function sendResponse($data, $message, $status = 200 ) {
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];

    return response()->json( $response );
}

function sendError( $errorData, $message, $status = 500 ) {
    $response = [];
    $response['status'] = 0;
    $response[ 'message' ] = $message;
    if ( !empty( $errorData ) ) {
        $response[ 'data' ] = $errorData;
    }

    return response()->json( $response, $status );
}

function customValidation(array $data,array $role){

    $validator = Validator::make($data,$role);
    if (count($validator->errors()) > 0) {
        return sendResponse($validator->errors(), 'validation error', 0);
    }

}
