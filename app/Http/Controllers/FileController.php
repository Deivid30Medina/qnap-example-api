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
     *     path="/api/v1/files/upload",
     *     tags={"File"},
     *     summary="Upload a file to QNAP",
     *     description="
     *  Example request:
     *      POST api/v1/files/upload?sid={value}&folderPath={value}
     *          
     *      form-data
     *          KEY: file
     *          VALUE: fileName.xxxx         
     * 
     *  Example of a successful response:
     *      {
     *          'status': 1,
     *          'size': '6274227',
     *          'name': 'example.pdf'
     *      }
     * 
     *  Example of an unsuccessful response:
     *  Refer to the documentation: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
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
     *                     description="User session ID"
     *                 ),
     *                 @OA\Property(
     *                     property="folderPath",
     *                     type="string",
     *                     description="Folder path where the file will be uploaded"
     *                 ),
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to upload"
     *                 ),
     *                 example={
     *                     "sid": "12345",
     *                     "folderPath": "/my/folder",
     *                     "file": "my_file.txt"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="1."),
     *             @OA\Property(property="size", type="string", example="4514151."),
     *             @OA\Property(property="name", type="string", example="fileName.xxxx"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content, documentation: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf",
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
     * Download a file from the specified folder
     * @OA\Get (
     *     path="/api/v1/files/download",
     *     tags={"File"},
     *     description="
     *  Example request:
     *      POST api/v1/files/download?sid={value}&folderPath={value}&fileName={value}
     *               
     *  Example of a successful response:
     *      File downloaded
     * 
     *  Example of an unsuccessful response:
     *  Refer to the documentation: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf
     *      404 not found
     *      ",
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
     *         description="Path where the file to be downloaded is located",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fileName",
     *         in="query",
     *         required=true,
     *         description="Name of the file, including extension, that you want to download",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File downloaded successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Content not found: https://download.qnap.com/dev/QNAP_QTS_File_Station_API_v5.pdf",
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
