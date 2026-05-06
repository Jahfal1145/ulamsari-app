<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PinController extends Controller
{
    public function index()
    {
        if (Session::has('block_until') && now()->lessThan(Session::get('block_until'))) {
            $remaining = now()->diffInSeconds(Session::get('block_until'));
            return view('auth.pin-login', compact('remaining'));
        }
        return view('auth.pin-login');
    }

    public function verify(Request $request)
    {
        $pin = $request->pin;
        $pinKasir = "111111"; 
        $pinDapur = "222222";
        $maxAttempts = 4;
        $lockTime = 60;

        if (Session::has('block_until') && now()->lessThan(Session::get('block_until'))) {
            return back()->with('error', 'Device diblokir sementara.');
        }

        if ($pin === $pinKasir || $pin === $pinDapur) {
            Session::forget(['pin_attempts', 'block_until']);
            
            if ($pin === $pinKasir) {
                session(['role' => 'kasir']);
                return redirect()->route('kasir.index');
            } else {
                session(['role' => 'dapur']);
                return redirect()->route('dapur.index');
            }
        }

        $attempts = Session::get('pin_attempts', 0) + 1;
        Session::put('pin_attempts', $attempts);

        if ($attempts >= $maxAttempts) {
            Session::put('block_until', now()->addSeconds($lockTime));
            Session::forget('pin_attempts');
            return back()->with('error', 'Terlalu banyak percobaan. Device diblokir 1 menit.');
        }

        $sisa = $maxAttempts - $attempts;
        return back()->with('error', "PIN Salah! Sisa kesempatan: $sisa kali.");
    }
}