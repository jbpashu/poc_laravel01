<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class ContextController extends Controller
{
     /**
     * Return the context.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContext(Request $request)
    {
        $user = User::where('name', $request->name)->get(['id', 'name', 'email','secret_token'])->first();

        if (is_null($user)) {
            return response()->json(['message' => 'Not found'], 404);
        } else {
            return response()->json(['data' => $user], 200);
        }
    }
}
