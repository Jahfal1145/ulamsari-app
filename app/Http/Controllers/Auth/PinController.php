<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PinController extends Controller
{
    // 1. Tampilkan Halaman PIN
    public function show(Request $request)
    {
        $lockedUntil = session('pin_locked_until');
        $remaining   = null;

        if ($lockedUntil) {
            $remaining = $lockedUntil - now()->timestamp;
            if ($remaining <= 0) {
                session()->forget(['pin_locked_until', 'pin_attempts']);
                $remaining = null;
            }
        }

        return view('auth.pin', compact('remaining'));
    }

    // 2. Proses Verifikasi (AUTO DETECT ROLE)
    public function verify(Request $request)
    {
        // Cek apakah masih dalam masa blokir
        $lockedUntil = session('pin_locked_until');
        if ($lockedUntil && now()->timestamp < $lockedUntil) {
            $remaining = $lockedUntil - now()->timestamp;
            return redirect()->route('pin.index')->with('error', "Akses diblokir. Tunggu {$remaining} detik.");
        }

        $inputPin = $request->input('pin');

        // Cek PIN ke masing-masing role langsung dari .env
        if ($inputPin === (string) env('PIN_ADMIN')) {
            return $this->loginSuccess('admin');
        } elseif ($inputPin === (string) env('PIN_KASIR')) {
            return $this->loginSuccess('kasir');
        } elseif ($inputPin === (string) env('PIN_DAPUR')) {
            return $this->loginSuccess('dapur');
        }

        // KALAU SALAH SEMUA: Tambah attempt
        $attempts = session('pin_attempts', 0) + 1;
        session(['pin_attempts' => $attempts]);

        if ($attempts >= 5) {
            session([
                'pin_locked_until' => now()->addSeconds(30)->timestamp,
                'pin_attempts'     => 0,
            ]);
            return redirect()->route('pin.index')->with('error', 'Terlalu banyak percobaan. Diblokir 30 detik.');
        }

        $sisa = 5 - $attempts;
        return redirect()->route('pin.index')->with('error', "PIN salah. Sisa percobaan: {$sisa}");
    }

    // Fungsi bantuan biar kodenya nggak kepanjangan
    private function loginSuccess($role)
    {
        session()->forget(['pin_attempts', 'pin_locked_until']);
        session(["auth_{$role}" => true]);
        return redirect()->route("{$role}.index");
    }

    // 3. Logout
    public function logout(Request $request, string $role)
    {
        session()->forget("auth_{$role}");
        return redirect()->route('pin.index');
    }
}