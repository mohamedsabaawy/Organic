<?php
namespace included;
function sendResponse( $data, $message, $status = 200 ) {
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
