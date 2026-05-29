<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kursus;
use App\Models\Paket;
use App\Models\Bahasa;
use App\Models\RegistrasiKursus;
use App\Models\RiwayatMateri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class MateriController extends Controller
{
    // GET /api/admin/materi
    public function index()
    {
        $materi = Materi::with('course.bahasa', 'course.paket')->get();

        return response()->json([
            'data' => $materi,
        ]);
    }

    // POST /api/admin/materi
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_paket' => 'required|exists:paket,id',
            'id_bahasa' => 'required|exists:bahasa,id',
            'level' => 'required|integer|min:1|max:3',
            'judul' => 'required|string|max:255',
            'url_video' => 'nullable|string|max:255', // boleh kamu hilangkan kalau mau
            'teks_teori' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mov,avi|max:204800', // ~200MB
        ]);

        $paket = Paket::findOrFail($data['id_paket']);
        $bahasa = Bahasa::findOrFail($data['id_bahasa']);

        $kursus = Kursus::firstOrCreate(
            ['id_paket' => $paket->id, 'id_bahasa' => $bahasa->id],
            ['deskripsi' => "Kursus {$bahasa->nama_bahasa} - Paket {$paket->nama_paket}"]
        );

        $materiData = [
            'id_course' => $kursus->id_course,
            'level' => $data['level'],
            'judul' => $data['judul'],
            'url_video' => $data['url_video'] ?? null,
            'teks_teori' => $data['teks_teori'] ?? null,
        ];

        // ⬅️ SIMPAN FILE VIDEO JIKA ADA
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('materi-video', 'public');
            $materiData['video_path'] = $path;

            // kalau mau benar-benar tidak pakai URL lagi
            $materiData['url_video'] = null;
        }

        $materiData['tipe'] = $this->resolveTipe(
            $materiData['url_video'] ?? null,
            $materiData['teks_teori'] ?? null,
            $materiData['video_path'] ?? null
        );

        $materi = Materi::create($materiData);
        $materi->load('course.bahasa', 'course.paket');

        return response()->json([
            'message' => 'Materi created',
            'data' => $materi,
        ], 201);
    }


    // PUT /api/admin/materi/{id_materi}
    public function update(Request $request, $id_materi)
    {
        $materi = Materi::findOrFail($id_materi);

        $data = $request->validate([
            'id_paket' => 'sometimes|exists:paket,id',
            'id_bahasa' => 'sometimes|exists:bahasa,id',
            'level' => 'sometimes|integer|min:1|max:3',
            'judul' => 'sometimes|string|max:255',
            'url_video' => 'sometimes|nullable|string|max:255',
            'teks_teori' => 'sometimes|nullable|string',
            'video_file' => 'sometimes|file|mimes:mp4,webm,ogg,mov,avi|max:204800',
        ]);

        // pindah kursus jika paket/bahasa diganti
        if (array_key_exists('id_paket', $data) || array_key_exists('id_bahasa', $data)) {
            $materi->load('course.bahasa', 'course.paket');

            $idPaket = $data['id_paket'] ?? $materi->course->id_paket;
            $idBahasa = $data['id_bahasa'] ?? $materi->course->id_bahasa;

            $paket = Paket::findOrFail($idPaket);
            $bahasa = Bahasa::findOrFail($idBahasa);

            $kursus = Kursus::firstOrCreate(
                ['id_paket' => $paket->id, 'id_bahasa' => $bahasa->id],
                ['deskripsi' => "Kursus {$bahasa->nama_bahasa} - Paket {$paket->nama_paket}"]
            );

            $data['id_course'] = $kursus->id_course;

            unset($data['id_paket'], $data['id_bahasa']);
        }

        // kalau admin upload video baru, replace yang lama
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('materi-video', 'public');
            $data['video_path'] = $path;
            $data['url_video'] = null; // kita pakai file saja
        }

        $url = $data['url_video'] ?? $materi->url_video;
        $teks = $data['teks_teori'] ?? $materi->teks_teori;
        $path = $data['video_path'] ?? $materi->video_path;

        $data['tipe'] = $this->resolveTipe($url, $teks, $path);

        $materi->update($data);
        $materi->load('course.bahasa', 'course.paket');

        return response()->json([
            'message' => 'Materi updated',
            'data' => $materi,
        ]);
    }

    // DELETE /api/admin/materi/{id_materi}
    public function destroy($id_materi)
    {
        $materi = Materi::findOrFail($id_materi);
        $materi->delete();

        return response()->json([
            'message' => 'Materi deleted',
        ]);
    }

    /**
     * tipe otomatis:
     * - teori     : cuma teks
     * - video     : cuma url_video
     * - campuran  : kedua-duanya ada
     * - kosong    : dua-duanya null
     */
    private function resolveTipe(?string $urlVideo, ?string $teksTeori, ?string $videoPath = null): string
    {
        // boleh pakai file atau url (ke depannya kamu bisa stop pakai url_video)
        $hasVideo = !empty($urlVideo) || !empty($videoPath);
        $hasText = !empty($teksTeori);

        if ($hasVideo && $hasText)
            return 'campuran';
        if ($hasVideo)
            return 'video';
        if ($hasText)
            return 'teori';
        return 'kosong';
    }

    // ==========================================
    // KHUSUS UNTUK MEMBER (FRONTEND)
    // ==========================================
    public function memberIndex()
    {
        // 1. Ambil semua materi dari Database
        // Kamu bisa memfilter berdasarkan paket/bahasa user nanti.
        // Untuk sekarang, kita ambil semua dulu biar tampil.
        $semuaMateri = Materi::orderBy('level')->get();

        // 2. Pisahkan Level 1, 2, dan 3
        // Kita "Map" (ubah format) datanya supaya cocok sama View kamu yang pakai array ['title']
        $materiLevel1 = $semuaMateri->where('level', 1)->map(function ($item) {
            return [
                'title' => $item->judul,
                'desc' => Str::limit(strip_tags($item->teks_teori), 100) ?? 'Belajar via Video', // Ambil cuplikan teks
                'img' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80', // Gambar default dulu
                'progress' => 0, // Nanti kita ambil dari tabel progress, sekarang 0 dulu biar jujur
                'slug' => Str::slug($item->judul, '-') // Buat link url
            ];
        });

        $materiLevel2 = $semuaMateri->where('level', 2)->map(function ($item) {
            return [
                'title' => $item->judul,
                'desc' => Str::limit(strip_tags($item->teks_teori), 100) ?? 'Lanjutan',
                'img' => 'https://images.unsplash.com/photo-1593642634367-d91a135587b5?auto=format&fit=crop&w=800&q=80',
                'progress' => 0,
                'slug' => Str::slug($item->judul, '-')
            ];
        });

        // 3. Kirim ke View (member/materi/index.blade.php)
        // Pastikan nama view-nya sesuai dengan folder kamu
        return view('member.materi.index', [
            'materiLevel1' => $materiLevel1,
            'materiLevel2' => $materiLevel2
        ]);
    }
    public function filter(Request $request)
    {
        $data = $request->validate([
            'paket' => 'required|exists:paket,id',   // FE kirim paket: id_paket
            'bahasa' => 'required|exists:bahasa,id', // FE kirim bahasa: id_bahasa
            'level' => 'nullable|integer|min:1|max:3',
        ]);

        $materi = Materi::with('course.bahasa', 'course.paket')
            ->whereHas('course', function ($q) use ($data) {
                $q->where('id_paket', $data['paket'])
                    ->where('id_bahasa', $data['bahasa']);
            })
            ->when(isset($data['level']), function ($q) use ($data) {
                $q->where('level', $data['level']);
            })
            ->orderBy('level')
            ->orderBy('judul')
            ->get();

        return response()->json([
            'data' => $materi,
        ]);
    }


    public function showBySlug($slug)
    {
        // Ekspektasi slug: "5-grammar" → kita ambil angka depannya = 5
        $parts = explode('-', $slug, 2);
        $id = (int) $parts[0];

        // Kalau id nggak valid
        if (!$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slug tidak valid',
            ], 400);
        }

        // Cari materi berdasarkan id_materi
        $materi = Materi::find($id);

        if (!$materi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Materi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $materi,
        ]);
    }



    // ==========================================
    // KHUSUS UNTUK MEMBER (PROGRESS & LIST)
    // ==========================================
    public function getMateriForMember(Request $request)
    {
        $idMember = $request->query('id_member');

        if (!$idMember) {
            return response()->json(['message' => 'ID Member diperlukan'], 400);
        }

        // 1. Kursus aktif / terakhir untuk member ini
        $registrasi = RegistrasiKursus::where('id_member', $idMember)
            ->orderByDesc('created_at')
            ->first();

        if (!$registrasi) {
            // Belum terdaftar kursus apa pun
            return response()->json(['data' => []]);
        }

        $idCourse = $registrasi->id_course;

        // 2. Ambil materi hanya untuk course tersebut
        $semuaMateri = Materi::with('course')
            ->where('id_course', $idCourse)
            ->orderBy('level')
            ->orderBy('id_materi')
            ->get();

        // 3. Ambil riwayat user ini
        $riwayat = RiwayatMateri::where('id_member', $idMember)->get();

        // 4. Gabungkan data (progress, slug, dll)
        $data = $semuaMateri->map(function ($item) use ($riwayat) {
            $history = $riwayat->firstWhere('id_materi', $item->id_materi);

            $progress = 0;
            if ($history) {
                if ($history->is_completed) {
                    $progress += 50;
                }
                if ($history->has_passed_quiz) {
                    $progress += 50;
                }
            }

            return [
                'id_materi' => $item->id_materi,
                'judul' => $item->judul,
                'level' => $item->level,
                'teks_teori' => $item->teks_teori,
                'url_video' => $item->url_video,
                'video_url' => $item->video_url,   // accessor dari model
                'progress' => $progress,
                'is_unlocked' => true,
                // ⬇️ slug unik: "5-grammar"
                'slug' => $item->id_materi . '-' . Str::slug($item->judul, '-'),
            ];
        })->values();

        return response()->json(['data' => $data]);
    }



    // POST /api/member/level-up
    public function levelUp(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'id_member' => 'required|integer',
            'current_level' => 'required|integer' // Level yang mau diselesaikan
        ]);

        $idMember = $request->id_member;
        $levelSelesai = $request->current_level;

        // 2. Cari Data Registrasi Kursus
        $reg = \App\Models\RegistrasiKursus::where('id_member', $idMember)->first();

        if (!$reg) {
            return response()->json(['message' => 'Member tidak terdaftar'], 404);
        }

        // 3. Cek Syarat: Apakah semua materi di level ini sudah beres?
        // Ambil ID materi di level ini
        $materiLevelIni = Materi::whereHas('course', function ($q) use ($reg) {
            $q->where('id_course', $reg->id_course);
        })->where('level', $levelSelesai)->pluck('id_materi');

        // Hitung berapa yang sudah completed di tabel riwayat
        $riwayatCount = RiwayatMateri::where('id_member', $idMember)
            ->whereIn('id_materi', $materiLevelIni)
            ->where('is_completed', true)
            ->count();

        // Kalau jumlah yang selesai < jumlah total materi, tolak!
        if ($riwayatCount < $materiLevelIni->count()) {
            return response()->json(['message' => 'Eits, selesaikan semua materi & kuis dulu ya!'], 403);
        }

        // 4. SAH! Naik Level (Update Database)
        if ($reg->last_unlocked_level <= $levelSelesai) {
            $reg->last_unlocked_level = $levelSelesai + 1;
            $reg->save();
        }

        return response()->json([
            'message' => 'Selamat! Level ' . ($levelSelesai + 1) . ' berhasil dibuka!',
            'new_level' => $reg->last_unlocked_level
        ]);
    }
}
