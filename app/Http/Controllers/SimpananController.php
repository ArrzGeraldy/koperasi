<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpananController extends Controller
{
    public function indexAnggota()
    {
        // Ambil simpanan user yang sedang login
        $user = Auth::user();
        $simpanan = $user->simpanan;
        
        // Hitung total simpanan
        $totalSimpanan = $simpanan ? (
            $simpanan->simpanan_pokok + 
            $simpanan->simpanan_wajib + 
            $simpanan->simpanan_sukarela
        ) : 0;
        
        return view('anggota-ui.simpanan.index', compact('simpanan', 'totalSimpanan', 'user'));
    }


}
