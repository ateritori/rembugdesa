<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AHP\AhpLogService;
use App\Models\DecisionSession;
use Illuminate\Contracts\View\View;

class AhpLogController extends Controller
{
    protected AhpLogService $ahpLogService;

    public function __construct(AhpLogService $ahpLogService)
    {
        $this->ahpLogService = $ahpLogService;

        // Catatan: Jika Anda menggunakan Laravel 11+, definisi middleware
        // disarankan dipindah ke file routes/web.php. Namun untuk Laravel 10
        // ke bawah, meletakkannya di sini masih sangat wajar.
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Tampilkan log AHP lengkap per session
     * Menggunakan Route Model Binding untuk langsung memanggil model
     */
    public function index($sessionId)
    {
        // Cari data manual seperti kode asli Anda
        $session = DecisionSession::findOrFail($sessionId);

        // Generate log lengkap
        $log = $this->ahpLogService->generateFullLog($session->id);

        // Pastikan keys utama ada (Null Coalescing Assignment)
        $log['criteria_names'] ??= [];
        $log['dm'] ??= [];
        $log['gm_final'] ??= [];

        // Kirim ke view
        return view('admin.ahp-log.index', compact('session', 'log'));
    }
}
