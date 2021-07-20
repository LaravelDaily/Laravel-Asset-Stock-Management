<?php

namespace App\Http\Middleware;

use App\Custom\TechKenLicense;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ValidateTechKen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // READ SYSCONFIG
        $tkl = new TechKenLicense();
        $license = $tkl->ReadLicense();

        // If INITVAL is zero
        if ($license['INITVAL'] == "0") {
            // INITIALIZE LICENSE VALUES
            // SET INITVAL TO 1
            $license['INITVAL'] = "1";
            // SET APPNAME
            $license['APPNAME'] = env("APP_NAME");
            // SET LICTYPE
            $license['LICTYPE'] = "TEMPORARY";
            // SET DATE_REG
            $license['LICDATEREG'] = Carbon::now()->format('Y-m-d');
            // SET DATE_EXP
            $days = (int) $license['LICDAYS'];  // GET DAYS FROM INITIAL LICENSE then add to expiry
            $license['LICDATEEXP'] = Carbon::now()->addDays($days)->format('Y-m-d');
            // SET DATE_NOW
            $license['LICDATENOW'] = Carbon::now()->format('Y-m-d');
            // SET UUID
            $sys_uuid = $tkl->GetUUID();
            $license['LICUUID'] = $sys_uuid;

            $data = $tkl->TechKenEncrypt(json_encode($license));
            Storage::put('sysconfig.tk', $data);
        } else if ($license['INITVAL'] == "1") {
            // IF UUID NOT SAME FROM LICUUID, REDIRECT TO LICENSE PAGE
            if ($license['LICUUID'] != $tkl->GetUUID()) {
                $request->session()->flash('message', 'System has been moved to another machine! Please contact developers.');
                Auth::logout();
                return redirect('login');
            }

            // IF(LICTYPE == TEMPORARY)
            if ($license['LICTYPE'] == "TEMPORARY") {
                // IF NOW >= LICDATEEXP, REDIRECT TO LICENSE PAGE
                if (Carbon::now()->startOfDay() >= Carbon::createFromFormat('Y-m-d', $license['LICDATEEXP'])->endOfDay())
                {
                    $request->session()->flash('message', 'Your license already expired. Please contact developers.');
                    Auth::logout();
                    return redirect('login');
                }

                // IF NOT (NOW >= LICDATENOW), REDIRECT TO LICENSE PAGE
                if (Carbon::now()->lt(Carbon::createFromFormat('Y-m-d', $license['LICDATENOW'])->startOfDay()))
                {
                    $request->session()->flash('message', 'System time has been changed. Please contact developers.');
                    Auth::logout();
                    return redirect('login');
                }
                else
                {
                    $license['LICDATENOW'] = Carbon::now()->format('Y-m-d');
                }

            }
        /* } elseif() {
            // IF THERE WILL BE OTHER VALUE TO CHECK SUCH AS RE-UPDATE (2) */
        } else {
            // IF THERE WILL BE OTHER VALUE TO CHECK SUCH AS RE-UPDATE (2)
            $request->session()->flash('message', 'Invalid license detected. Please contact developers.');
            Auth::logout();
            return redirect('login');
        }

        return $next($request);
    }
}
