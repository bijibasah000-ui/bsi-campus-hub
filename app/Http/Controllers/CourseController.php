<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    private function allCourses()
    {
        return [
            ['icon'=>'💻','name'=>'Pemrograman Web',    'meta'=>'Semester 5 · 12 Modul · Pak Budi S.', 'bg'=>'#EEF2FF','badge_color'=>'green', 'badge_label'=>'Tersedia','slug'=>'pemrograman-web'],
            ['icon'=>'🗄️','name'=>'Basis Data Lanjutan', 'meta'=>'Semester 5 · 10 Modul · Bu Sari W.',  'bg'=>'#FFF7ED','badge_color'=>'green', 'badge_label'=>'Tersedia','slug'=>'basis-data-lanjutan'],
            ['icon'=>'🔐','name'=>'Keamanan Sistem',     'meta'=>'Semester 5 · 9 Modul · Pak Hendra',   'bg'=>'#F0FFF4','badge_color'=>'blue',  'badge_label'=>'Baru',    'slug'=>'keamanan-sistem'],
            ['icon'=>'📊','name'=>'Analisis Algoritma',  'meta'=>'Semester 5 · 8 Modul · Bu Rini A.',   'bg'=>'#FFF1F2','badge_color'=>'amber', 'badge_label'=>'Update',  'slug'=>'analisis-algoritma'],
            ['icon'=>'🌐','name'=>'Jaringan Komputer',   'meta'=>'Semester 5 · 11 Modul · Pak Doni',    'bg'=>'#F0F4FF','badge_color'=>'green', 'badge_label'=>'Tersedia','slug'=>'jaringan-komputer'],
            ['icon'=>'🤖','name'=>'Kecerdasan Buatan',   'meta'=>'Semester 5 · 7 Modul · Bu Nita K.',   'bg'=>'#FFFBEB','badge_color'=>'blue',  'badge_label'=>'Baru',    'slug'=>'kecerdasan-buatan'],
        ];
    }

    public function index()
    {
        $courses = $this->allCourses();
        return view('course.index', compact('courses'));
    }

    public function show($slug)
    {
        $all    = collect($this->allCourses());
        $course = $all->firstWhere('slug', $slug) ?? $all->first();
        return view('course.show', compact('course'));
    }
}
