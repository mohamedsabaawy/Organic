<?php

namespace included;

use Illuminate\Support\Facades\Validator;

function sendResponse($data, $message, $status = 200)
{
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];

    return response()->json($response);
}

function sendError($errorData, $message, $status = 500)
{
    $response = [];
    $response['status'] = 0;
    $response['message'] = $message;
    if (!empty($errorData)) {
        $response['data'] = $errorData;
    }

    return response()->json($response, $status);
}

function getPrice($item, $type = 'item')
{
    if ($type === 'offer')
        return request()->ipinfo->country == "EG" ? $item->price : $item->price_dollar;
//    return $item->price;
    elseif ($type = 'item')
        return request()->ipinfo->country == "EG" ? $item->price : $item->price_dollar;
}

function getDiscount($item, $type = 'item')
{
    return request()->ipinfo->country == "EG" ? $item->discount : $item->discount_dollar;
}

function customValidation(array $data, array $role)
{

    $validator = Validator::make($data, $role);
    if (count($validator->errors()) > 0) {
        return sendResponse($validator->errors(), 'validation error', 0);
    }

}
