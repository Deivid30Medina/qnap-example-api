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
     * Validate if a folder exists in a specific location
     * @OA\Get (
     *     path="/api/v1/dnda/folder/exists",
     *     tags={"Folder"},
     *     description="
     *  Example request:
     *      POST api/v1/dnda/folder/exists?sid={value}&folderPath={value}&folderName={value}
     *               
     *  Example of a successful response:
     *      True - If the folder already exists in the location
     * 
     *  Example of an unsuccessful response:
     *      False - If the folder does not exist in the location
     *      ",
     * 
     *     @OA\Parameter(
     *         name="sid",
     *         in="query",
     *         required=true,
     *         description="User session ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderPath",
     *         in="query",
     *         required=true,
     *         description="Location of the folder to validate",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderName",
     *         in="query",
     *         required=true,
     *         description="Name of the folder to validate",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="True if the folder exists in the location.",
     *      @OA\JsonContent(
     *             @OA\Property(property="true", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="False if the folder does not exist in the location.",
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
     * Create a folder in a specified location
     * @OA\Post (
     *     path="/api/v1/dnda/folder/create",
     *     tags={"Folder"},
     *     description="
     *  Example request:
     *      POST api/v1/dnda/folder/create?sid={value}&folderPath={value}&folderName={value}
     *               
     *  Example of a successful response:
     *      {
     *          'version': '5.5.5',
     *          'build': '20240817',
     *          'status': 1,
     *          'success': 'true'
     *      }
     * 
     *  Example of an unsuccessful response:
     *  Refer to the documentation: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
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
     *         description="User session ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderPath",
     *         in="query",
     *         required=true,
     *         description="Location where the folder will be created",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="folderName",
     *         in="query",
     *         required=true,
     *         description="Name of the folder to be created",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="If the creation was successful.",
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
