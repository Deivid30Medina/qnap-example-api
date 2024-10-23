<?php

namespace App\Services;

use App\Models\FolderSearch;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;


class QnapService
{
    protected $httpClient;
    protected $nasSettings; // Supón que esto tiene tus configuraciones de NAS

    public function __construct()
    {
        $this->httpClient = new Client([
            'verify' => false, // Ignorar la verificación del certificado SSL
        ]);
        $this->nasSettings = config('nas');
    }

    public function uploadFile(string $folderPath, UploadedFile $file, string $sid)
    {
        $routeWithoutPath = str_replace('/', '-', $folderPath);
        $progress = "{$routeWithoutPath}-{$file->getClientOriginalName()}";
        error_log($progress);

        $url = "{$this->nasSettings['urlQnap']}/filemanager/utilRequest.cgi?sid={$sid}&func=upload&type=standard&dest_path={$folderPath}&overwrite=1&progress={$progress}";

        try {
            // Crea el contenido multipart
            $response = $this->httpClient->post($url, [
                'multipart' => [
                    [
                        'name' => '',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ]
                ]
            ]);


            $responseContent = $response->getBody()->getContents();

            $foldersData = json_decode($responseContent, true);


            return $foldersData;
        } catch (\Exception $e) {
            error_log("Error: " . $e->getMessage());
            return response()->json(['error' => 'Upload failed.'], 500);
        }
    }

    public function existFolder(string $sid, string $folderPath, string $folderName)
    {
        // error_log(print_r($foldersData, true));
        $url = "{$this->nasSettings['urlQnap']}/filemanager/utilRequest.cgi?func=get_tree&is_iso=0&sid={$sid}&node={$folderPath}";

        try {
            $response = $this->httpClient->get($url);
            $responseContent = $response->getBody()->getContents();

            // Deserializar el contenido JSON a un arreglo de objetos FolderSearch
            $foldersData = json_decode($responseContent, true);
            $folderExists = false;

            foreach ($foldersData as $folder) {
                if ($folder['text'] === $folderName) {
                    $folderExists = true;
                    break;
                }
            }

            return $folderExists;
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception('Failed to check folder existence');
        }
    }

    public function createFolder(string $sid, string $folderPath, string $folderName)
    {
        // error_log(print_r($foldersData, true));
        $url = "{$this->nasSettings['urlQnap']}/filemanager/utilRequest.cgi?func=createdir&sid={$sid}&dest_folder={$folderName}&dest_path={$folderPath}";

        try {
            $response = $this->httpClient->post($url);
            $responseContent = $response->getBody()->getContents();

            // Deserializar el contenido JSON a un arreglo de objetos FolderSearch
            $foldersData = json_decode($responseContent, true);

            return $foldersData;
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception('Failed to check folder existence');
        }
    }

    public function dowloadFile(string $sid, string $folderPath, string $fileName)
    {
        // error_log(print_r($foldersData, true));

        $url = "{$this->nasSettings['urlQnap']}/filemanager/utilRequest.cgi?func=download&sid={$sid}&compress=0&source_path={$folderPath}&source_file={$fileName}&source_total=1";

        try {
            $response = $this->httpClient->get($url);
            $responseContent = $response->getBody()->getContents();

            // Determinar el tipo de contenido
            $contentType = $response->getHeaderLine('Content-Type') ?: 'application/octet-stream';

            // Preparar la cabecera para la respuesta
            $headers = [
                'Content-Description' => 'File Transfer',   //Describe que el contenido es una transferencia de archivo.
                'Content-Type' => $contentType,             //Se utiliza el tipo de contenido previamente obtenido.
                'Content-Disposition' => 'attachment; filename="' . basename($fileName) . '"', //Indica al navegador que debe tratar el contenido como un archivo adjunto
                'Expires' => '0', //Se establece en 0 para que el archivo no sea almacenado en caché.
                'Cache-Control' => 'must-revalidate', //Se establece en must-revalidate para que el cliente valide la versión del archivo en el servidor antes de usarla.
                'Pragma' => 'public', //Se establece como public para permitir el almacenamiento en caché en proxies públicos.
                'Content-Length' => strlen($responseContent), //Se obtiene la longitud del contenido, que es necesaria para que el navegador sepa cuánto contenido esperar.
            ];

            // Retornar el contenido y las cabeceras
            return [
                'headers' => $headers,
                'content' => $responseContent,
            ];
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception('Failed to check folder existence: ' . $e->getMessage()); // Usar . en lugar de +
        }
    }

    public function logIn(string $username, string $password)
    {
        // dd($url); // Puedes dejar esta línea para depuración si es necesario
        // error_log(print_r($foldersData, true));
        $url = "{$this->nasSettings['urlQnap']}/authLogin.cgi?user={$username}&pwd={$password}";

        try {
            $response = $this->httpClient->post($url);
            $responseContent = $response->getBody()->getContents();

            $xml = simplexml_load_string($responseContent);

            // Buscar el elemento 'authSid'
            $authSidElement = $xml->xpath('//authSid');

            // Retornar el valor de 'authSid' o un mensaje de error si no se encuentra
            return !empty($authSidElement) ? (string) $authSidElement[0] : 'Usuario o clave incorrecto';
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception('Failed to check folder existence');
        }
    }
}
