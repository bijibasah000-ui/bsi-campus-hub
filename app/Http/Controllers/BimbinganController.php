<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class BimbinganController extends Controller
{
    public function index()
    {
        $dosens = [
            ['initials'=>'BS','name'=>'Dr. Budi Santoso',  'role'=>'Pembimbing Skripsi', 'av_bg'=>'#EEF2FF','av_color'=>'#4F46E5','badge_color'=>'green', 'badge_label'=>'Slot Tersedia'],
            ['initials'=>'SW','name'=>'Ibu Sari Wulandari','role'=>'Bimbingan Akademik', 'av_bg'=>'#FFF0F9','av_color'=>'#BE185D','badge_color'=>'amber', 'badge_label'=>'2 Slot Sisa'],
            ['initials'=>'HK','name'=>'Pak Hendra Kurnia', 'role'=>'KP / PKL',           'av_bg'=>'#ECFDF5','av_color'=>'#059669','badge_color'=>'green', 'badge_label'=>'Slot Tersedia'],
            ['initials'=>'RA','name'=>'Bu Rini Astuti',    'role'=>'Tugas Akhir',        'av_bg'=>'#FFFBEB','av_color'=>'#B45309','badge_color'=>'red',   'badge_label'=>'Penuh'],
        ];
        return view('bimbingan.index', compact('dosens'));
    }
}
