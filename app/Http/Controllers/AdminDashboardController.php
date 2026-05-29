<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RegistrasiKursus;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard/summary
     */
    public function summary()
    {
        try {
            $pendingVerifications = User::where('role', 'member')
                ->whereNull('email_verified_at')
                ->count();

            $totalMembers = User::where('role', 'member')->count();

            $newMembersThisWeek = User::where('role', 'member')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            $paketStats = RegistrasiKursus::selectRaw('paket.nama_paket AS label, COUNT(*) AS total')
                ->join('kursus', 'registrasi_kursus.id_course', '=', 'kursus.id_course')
                ->join('paket', 'kursus.id_paket', '=', 'paket.id')
                ->groupBy('paket.nama_paket')
                ->orderByDesc('total')
                ->get();

            $paketLabels = $paketStats->pluck('label');
            $paketData = $paketStats->pluck('total');

            $bahasaStats = RegistrasiKursus::selectRaw('bahasa.nama_bahasa AS label, COUNT(*) AS total')
                ->join('kursus', 'registrasi_kursus.id_course', '=', 'kursus.id_course')
                ->join('bahasa', 'kursus.id_bahasa', '=', 'bahasa.id')
                ->groupBy('bahasa.nama_bahasa')
                ->orderByDesc('total')
                ->get();

            $bahasaLabels = $bahasaStats->pluck('label');
            $bahasaData = $bahasaStats->pluck('total');

            // HATI2: ini Postgres, pakai operator ||
            $courseStats = RegistrasiKursus::selectRaw("
                    (bahasa.nama_bahasa || ' - ' || paket.nama_paket) AS label,
                    COUNT(*) AS total
                ")
                ->join('kursus', 'registrasi_kursus.id_course', '=', 'kursus.id_course')
                ->join('bahasa', 'kursus.id_bahasa', '=', 'bahasa.id')
                ->join('paket', 'kursus.id_paket', '=', 'paket.id')
                ->groupBy('bahasa.nama_bahasa', 'paket.nama_paket')
                ->orderByDesc('total')
                ->get();

            $courseLabels = $courseStats->pluck('label');
            $courseData = $courseStats->pluck('total');

            return response()->json([
                'pending_verifications' => $pendingVerifications,
                'total_members' => $totalMembers,
                'new_members_this_week' => $newMembersThisWeek,
                'paket' => [
                    'labels' => $paketLabels,
                    'data' => $paketData,
                ],
                'bahasa' => [
                    'labels' => $bahasaLabels,
                    'data' => $bahasaData,
                ],
                'kursus' => [
                    'labels' => $courseLabels,
                    'data' => $courseData,
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error dashboard summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
