<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;


// TIP: auth:sanctum middleware will use this controller to protect all routes 
// which use it.

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        // Compare input password with stored password in database
        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "error" => "The provided credentials are incorrect."
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Otherwise generate the token for that device e.g. Mobile e.t.c from the Header
        $device = substr($request->userAgent() ?? "", 0, 255);

        return response()->json([
            "access_token" => $user->createToken($device)->plainTextToken,
        ]);
    }
}
