<?php

namespace App\Custom;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TechKenLicense
{
    public $cipher = "AES-128-CTR";
    public $key = "TechKenGuard";
    public $iv = "0123456789101112";
    public $opt = 0;

    public function TechKenEncrypt($data)
    {
        try {
            $result = openssl_encrypt(
                $data,
                $this->cipher,
                $this->key,
                $this->opt,
                $this->iv
            );
            return $result;
        } catch (Exception $ex) {
            Log::error("Critical problem. Contact support now.", [$ex->getMessage()]);
        }
    }

    public function TechKenDecrypt($data)
    {
        try {
            $result = openssl_decrypt(
                $data,
                $this->cipher,
                $this->key,
                $this->opt,
                $this->iv
            );
            return $result;
        } catch (Exception $ex) {
            Log::error("Critical problem. Contact support now.", [$ex->getMessage()]);
        }
    }

    public function GetUUID()
    {
        // Checking UUID for Windows
        $process = shell_exec("C:\\Windows\\System32\\wbem\\WMIC.exe csproduct get UUID");
        $process = str_replace("UUID", "", $process);
        $process = str_replace(" ", "", $process);
        $process = str_replace("\r\n", "", $process);

        return $process;
    }

    public function ReadLicense()
    {
        $data = Storage::get('sysconfig.tk');
        $response = json_decode($this->TechKenDecrypt($data), true);
        return $response;
    }
}
