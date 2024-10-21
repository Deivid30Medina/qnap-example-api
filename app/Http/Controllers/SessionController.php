<?php

namespace App\Http\Controllers;

use App\Services\QnapService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    protected $qnapService;

    public function __construct(QnapService $qnapService)
    {
        $this->qnapService = $qnapService;
    }

    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->qnapService->logIn($request->input('username'), $request->input('password'));

        return response()->json($result);
    }
}
