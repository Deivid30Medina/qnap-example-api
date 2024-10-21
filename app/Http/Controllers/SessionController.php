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
     * Obtenner un token Sid
     * @OA\Post (
     *     path="/api/v1/dnda/session/login",
     *     tags={"Sesion"},
     *     description=" 
     *  Ejemplo de request:
     *      POST api/v1/dnda/session/login?sid={value}&folderPath={value}&fileName={value}
     *               
     *  Ejemplo de respuesta correcta:
     *      {Sid}
     * 
     *  Ejemplo de respuesta incorrecta
     *  Consulte la documentación: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
     *      403 Forbidden
     *      ",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="Usuario qnap",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="Clave codificada en base 64",
     *         @OA\Schema(type="string")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Token Sid",
     *      @OA\JsonContent(
     *             @OA\Property(property="dasjdhsano", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso Denegado",
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

        return response()->json($result);
    }
}
