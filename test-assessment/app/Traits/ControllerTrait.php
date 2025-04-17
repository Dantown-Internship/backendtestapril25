<?php

namespace App\Traits;

use App\Mail\passwordResetLinkMail;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait ControllerTrait
{
    
    public function successResponse(string $message, $data, $code)
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'message' => $message,
            'data' => is_array($data) ? $data : $data->toArray()
        ], $code);
    }

    public function failureResponse(string $message, $data, $code)
    {
        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
