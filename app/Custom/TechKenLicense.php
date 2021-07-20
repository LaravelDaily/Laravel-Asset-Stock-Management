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
        try {
            switch(PHP_OS_FAMILY) {
                case "Windows":
                    // Checking UUID for Windows
                    $process = shell_exec("C:\\Windows\\System32\\wbem\\WMIC.exe csproduct get UUID");
                    $process = str_replace("UUID", "", $process);
                    $process = str_replace(" ", "", $process);
                    $process = str_replace("\r\n", "", $process);
                    break;
                case "Linux":
                    $process = shell_exec("blkid /dev/sda1");
                    $process = str_replace("\r\n", "", $process);
                    $arr_pro = explode(" ", $process);
                    foreach($arr_pro as $pro)
                    {
                        if(str_contains($pro, "UUID"))
                        {
                            $pro = str_replace("UUID=", "", $pro);
                            $pro = str_replace('"', '', $pro);
                            $process = $pro;
                            break;
                        }
                    }
                    break;
                default:
            }
            return $process;
        } catch(Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function ReadLicense()
    {
        $data = Storage::get('sysconfig.tk');
        $response = json_decode($this->TechKenDecrypt($data), true);
        return $response;
    }
}
