<?php

namespace App\Http\Controllers;

use App\Services\QnapService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $qnapService;

    public function __construct(QnapService $qnapService)
    {
        $this->qnapService = $qnapService;
    }

    /**
     * Subir un archivo a la carpeta especificada https://download.qnap.com/dev/API_QNAP_QTS_Authentication.pdf
     * @OA\Post (
     *     path="/api/archivos/upload",
     *     tags={"Archivo"},
     *     summary="Subir un archivo a QNAP",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="sid",
     *                     type="string",
     *                     description="ID de sesión del usuario"
     *                 ),
     *                 @OA\Property(
     *                     property="folderPath",
     *                     type="string",
     *                     description="Ruta de la carpeta donde se subirá el archivo"
     *                 ),
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="Archivo a subir"
     *                 ),
     *                 example={
     *                     "sid": "12345",
     *                     "folderPath": "/mi/carpeta",
     *                     "file": "mi_archivo.txt"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo subido correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Archivo subido exitosamente."),
     *             @OA\Property(property="result", type="object", 
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="uploadedFile", type="string", example="mi_archivo.txt"),
     *                 @OA\Property(property="folderPath", type="string", example="/mi/carpeta")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Contenido no procesable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El campo folderPath es obligatorio."),
     *             @OA\Property(property="errors", type="object", 
     *                 @OA\Property(property="folderPath", type="array", @OA\Items(type="string"), example={"El campo folderPath es obligatorio."})
     *             )
     *         )
     *     )
     * )
     */
    public function upload(Request $request)
    {
        $request->validate([
            'sid' => 'required|string',
            'folderPath' => 'required|string',
            'file' => 'required|file',
        ]);

        $result = $this->qnapService->uploadFile($request->input('folderPath'), $request->file('file'), $request->input('sid'));

        return response()->json($result);
    }


    /**
     * Descargar un archivo desde la carpeta especificada
     * @OA\Get (
     *     path="/api/archivos/download",
     *     tags={"Archivo"},
     *     @OA\Parameter(
     *         name="sid",
     *         in="query",
     *         required=true,
     *         description="ID de sesión del usuario",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filePath",
     *         in="query",
     *         required=true,
     *         description="Ruta del archivo que se desea descargar",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo descargado correctamente",
     *         @OA\MediaType(
     *             mediaType="application/octet-stream",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El archivo solicitado no se encontró."),
     *             @OA\Property(property="filePath", type="string", example="/mi/carpeta/mi_archivo.txt")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Contenido no procesable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El campo filePath es obligatorio."),
     *             @OA\Property(property="errors", type="object", 
     *                 @OA\Property(property="filePath", type="array", @OA\Items(type="string"), example={"El campo filePath es obligatorio."})
     *             )
     *         )
     *     )
     * )
     */
    public function download(Request $request)
    {
        // Lógica para descargar el archivo
    }
}
