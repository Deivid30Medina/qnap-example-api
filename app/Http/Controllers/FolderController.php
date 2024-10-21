<?php

namespace App\Http\Controllers;

use App\Services\QnapService;
use Exception;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    protected $qnapService;

    public function __construct(QnapService $qnapService)
    {
        $this->qnapService = $qnapService;
    }

    /**
     * Validar si una carpeta existe en una ubicación especifica
     * @OA\Get (
     *     path="/api/v1/dnda/folder/exists",
     *     tags={"Folder"},
     *     description=" 
     *  Ejemplo de request:
     *      POST api/v1/dnda/folder/exists?sid={value}&folderPath={value}&folderName={value}
     *               
     *  Ejemplo de respuesta correcta:
     *      True - Si la carpeta ya existía en la ubicación 
     * 
     *  Ejemplo de respuesta incorrecta
     *      False - Si la carpeta no existe en la ubicación
     *      ",
     * 
     *     @OA\Parameter(
     *         name="sid",
     *         in="query",
     *         required=true,
     *         description="ID de sesión del usuario",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderPath",
     *         in="query",
     *         required=true,
     *         description="Ubicación de la carpeta a validar",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderName",
     *         in="query",
     *         required=true,
     *         description="Nombre de la carpeta a validar",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="True si existe la carpeta en la ubicación.",
     *      @OA\JsonContent(
     *             @OA\Property(property="true", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="False si no existe la carpeta en la ubicación.",
     *      @OA\JsonContent(
     *             @OA\Property(property="false", type="boolean"),
     *         )
     *     ),
     * )
     */
    public function exists(Request $request)
    {

        $request->validate([
            'sid' => 'required|string',
            'folderPath' => 'required|string',
            'folderName' => 'required|string',
        ]);

        try {
            $result = $this->qnapService->existFolder(
                $request->input('sid'),
                $request->input('folderPath'),
                $request->input('folderName')
            );
            if ($result == true) {
                return response()->json($result, 200);
            }
            return response()->json($result, 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);  // Error de servidor
        }
    }

    /**
     * Crear una carpeta en una ubicación especificada
     * @OA\Post (
     *     path="/api/v1/dnda/folder/create",
     *     tags={"Folder"},
     *     description=" 
     *  Ejemplo de request:
     *      POST api/v1/dnda/folder/create?sid={value}&folderPath={value}&folderName={value}
     *               
     *  Ejemplo de respuesta correcta:
     *      {
     *          'version': '5.5.5',
     *          'build': '20240817',
     *          'status': 1,
     *          'success': 'true'
     *      }
     * 
     *  Ejemplo de respuesta incorrecta
     *  Consulte la documentación: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
     *      {
     *          'version': '5.5.5',
     *          'build': '20240817',
     *          'status': 33,
     *          'success': 'true'
     *      }
     * ",
     * 
     *     @OA\Parameter(
     *         name="sid",
     *         in="query",
     *         required=true,
     *         description="ID de sesión del usuario",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderPath",
     *         in="query",
     *         required=true,
     *         description="Ubicación donde se va a crear la carpeta",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderName",
     *         in="query",
     *         required=true,
     *         description="Nombre de la carpeta a crear",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Si la creación fue exitosa.",
     *      @OA\JsonContent(
     *             @OA\Property(property="version", type="string", example="5.5.5"),
     *             @OA\Property(property="build", type="string", example="20240817"),
     *             @OA\Property(property="status", type="number", example="1"),
     *             @OA\Property(property="success", type="string", example="true"),
     *         )
     *     ),
     * )
     */
    public function create(Request $request)
    {

        $request->validate([
            'sid' => 'required|string',
            'folderPath' => 'required|string',
            'folderName' => 'required|string',
        ]);

        try {
            $result = $this->qnapService->createFolder(
                $request->input('sid'),
                $request->input('folderPath'),
                $request->input('folderName')
            );
            return response()->json($result, 200);  // Respuesta exitosa
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);  // Error de servidor
        }
    }
}
