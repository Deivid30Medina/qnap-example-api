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

    public function exists(Request $request){

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
            return response()->json($result, 200);  // Respuesta exitosa
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);  // Error de servidor
        }
    }

    public function create(Request $request){

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
