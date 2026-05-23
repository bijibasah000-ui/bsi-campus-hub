<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $events = [
            ['day'=>'22','month'=>'Apr','title'=>'UTS Algoritma & Pemrograman',   'label'=>'Ujian',  'color'=>'red',   'desc'=>'Ruang 301 · 08.00 WIB'],
            ['day'=>'25','month'=>'Apr','title'=>'Seminar Kewirausahaan',          'label'=>'Seminar','color'=>'blue',  'desc'=>'Aula Kampus'],
            ['day'=>'28','month'=>'Apr','title'=>'Deadline Tugas Basis Data',      'label'=>'Tugas',  'color'=>'amber', 'desc'=>'Upload e-learning'],
            ['day'=>'01','month'=>'Mei','title'=>'Libur Hari Buruh Nasional',      'label'=>'Libur',  'color'=>'green', 'desc'=>'Kampus tutup'],
        ];

        $announcements = [
            ['day'=>'20','month'=>'Apr','title'=>'Jadwal Bimbingan Skripsi Dibuka','label'=>'Bimbingan',   'color'=>'green', 'desc'=>'Daftar sekarang'],
            ['day'=>'19','month'=>'Apr','title'=>'Lapak Baru di Pojok Nyemil!',    'label'=>'Pojok Nyemil','color'=>'orange','desc'=>'"Dapur Kita" buka'],
            ['day'=>'18','month'=>'Apr','title'=>'Materi Pemrograman Web Diperbarui','label'=>'Materi',   'color'=>'blue',  'desc'=>'Modul 7 tersedia'],
            ['day'=>'17','month'=>'Apr','title'=>'Pendaftaran PKL Semester 6',     'label'=>'PKL',        'color'=>'amber', 'desc'=>'Batas 30 Apr'],
        ];

        $recentCourses = [
            ['icon'=>'💻','name'=>'Pemrograman Web',   'meta'=>'Semester 5 · TI','slug'=>'pemrograman-web'],
            ['icon'=>'🗄️','name'=>'Basis Data Lanjutan','meta'=>'Semester 5 · TI','slug'=>'basis-data-lanjutan'],
            ['icon'=>'🔐','name'=>'Keamanan Sistem',   'meta'=>'Semester 5 · TI','slug'=>'keamanan-sistem'],
        ];

        return view('dashboard.index', compact('events','announcements','recentCourses'));
    }
}
