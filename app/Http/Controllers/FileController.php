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
     * @OA\Post (
     *     path="/api/v1/dnda/files/upload",
     *     tags={"File"},
     *     summary="Subir un archivo a QNAP",
     *      description=" 
     *  Ejemplo de request:
     *      POST api/v1/dnda/files/upload?sid={value}&folderPath={value}
     *          
     *      form-data
     *          KEY: file
     *          VALUE: fileName.xxxx         
     * 
     *  Ejemplo de respuesta correcta:
     *      {
     *          'status': 1,
     *          'size': '6274227',
     *          'name': 'example.pdf'
     *      }
     * 
     *  Ejemplo de respuesta incorrecta
     *  Consulte la documentación: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
     *      {
     *          'version': '5.5.5',
     *          'build': '20240817',
     *          'status': 9,
     *          'success': 'true'
     *      }
     *      ",
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
     *             @OA\Property(property="status", type="string", example="1."),
     *             @OA\Property(property="size", type="string", example="4514151."),
     *             @OA\Property(property="name", type="string", example="fileName.xxxx"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Contenido no procesable documentacion: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf",
     *         @OA\JsonContent(
     *             @OA\Property(property="version", type="string", example="5.5.5"),
     *             @OA\Property(property="build", type="string", example="20240817"),
     *             @OA\Property(property="status", type="number", example="9"),
     *             @OA\Property(property="success", type="string", example="true"),
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
     *     path="/api/v1/dnda/files/download",
     *     tags={"File"},
     *     description=" 
     *  Ejemplo de request:
     *      POST api/v1/dnda/files/download?sid={value}&folderPath={value}&fileName={value}
     *               
     *  Ejemplo de respuesta correcta:
     *      Archivo descargado
     * 
     *  Ejemplo de respuesta incorrecta
     *  Consulte la documentación: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
     *      404 not found
     *      ",
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
     *         description="Ruta donde se encuentra el archivo que se desea descargar",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fileName",
     *         in="query",
     *         required=true,
     *         description="Nombre del archivo con extención que se desea",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo descargado correctamente",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contenido no encontrado: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf",
     *     )
     * )
     */
    public function download(Request $request)
    {

        $request->validate([
            'sid' => 'required|string',
            'folderPath' => 'required|string',
            'fileName' => 'required|string',
        ]);

        $result = $this->qnapService->dowloadFile($request->input('sid'), $request->input('folderPath'), $request->input('fileName'));

        // Configurar las cabeceras para la respuesta HTTP
        foreach ($result['headers'] as $header => $value) {
            header("$header: $value");
        }

        // Enviar el contenido del archivo
        echo $result['content'];
        exit; // Terminar el script después de enviar el archivo
    }
}
