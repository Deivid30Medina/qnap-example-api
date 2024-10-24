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

    /**
     * Obtain a Sid token
     * @OA\Post (
     *     path="/api/v1/session/login",
     *     tags={"Session"},
     *     description="
     *  Example request:
     *      POST api/v1/session/login?username={value}&password={value}
     *               
     *  Example of a successful response:
     *      {Sid}
     * 
     *  Example of an incorrect response:
     *  Refer to the documentation: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
     *      403 Forbidden
     *      ",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="QNAP user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="Base64 encoded password",
     *         @OA\Schema(type="string")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Sid Token",
     *      @OA\JsonContent(
     *             @OA\Property(property="dasjdhsano", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access Denied",
     *     )
     * )
     */
    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->qnapService->logIn($request->input('username'), $request->input('password'));

        return $result;
    }
}
