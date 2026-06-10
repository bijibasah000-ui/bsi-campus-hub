<?php
namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\Penukaran;
use App\Models\PoinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RewardController extends Controller
{
    public function index()
    {
        $rewards   = Reward::where('aktif', true)->orderBy('poin_dibutuhkan')->get();
        $user      = Auth::user();
        $riwayat   = Penukaran::with('reward')->where('user_id', $user->id)->latest()->take(10)->get();
        $poinLogs  = PoinLog::where('user_id', $user->id)->latest()->take(10)->get();
        return view('reward.index', compact('rewards','user','riwayat','poinLogs'));
    }

    public function tukar(Request $request, Reward $reward)
    {
        $user = Auth::user();

        if ($user->poin < $reward->poin_dibutuhkan) {
            return response()->json(['ok' => false, 'msg' => 'Poin tidak cukup! Kamu butuh '.($reward->poin_dibutuhkan - $user->poin).' poin lagi.']);
        }

        if ($reward->stok === 0) {
            return response()->json(['ok' => false, 'msg' => 'Stok reward sudah habis.']);
        }

        // Kurangi poin
        $user->decrement('poin', $reward->poin_dibutuhkan);

        // Kurangi stok jika bukan unlimited
        if ($reward->stok > 0) $reward->decrement('stok');

        // Buat kode klaim unik
        $kode = strtoupper(Str::random(8));

        // Log penukaran
        $penukaran = Penukaran::create([
            'user_id'     => $user->id,
            'reward_id'   => $reward->id,
            'poin_dipakai'=> $reward->poin_dibutuhkan,
            'status'      => 'pending',
            'kode_klaim'  => $kode,
        ]);

        // Log poin
        PoinLog::create([
            'user_id'    => $user->id,
            'jumlah'     => -$reward->poin_dibutuhkan,
            'keterangan' => 'Penukaran: '.$reward->nama,
            'referensi'  => 'reward_'.$reward->id,
            'tipe'       => 'penukaran',
        ]);

        return response()->json([
            'ok'         => true,
            'kode'       => $kode,
            'poin_sisa'  => $user->fresh()->poin,
            'reward'     => $reward->nama,
        ]);
    }
}
