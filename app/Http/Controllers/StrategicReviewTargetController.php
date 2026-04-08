<?php

namespace App\Http\Controllers;

use App\Models\matriks_fra as MatriksFra;
use App\Models\target_pk as TargetPK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StrategicReviewTargetController extends Controller
{
    public function simpanTargetPK(Request $request, $kegiatanId)
    {
        try {
            DB::beginTransaction();

            // Get all matriks_fra data for this kegiatan
            $matriksFraData = MatriksFra::whereHas('template_fra.kegiatan', function ($query) use ($kegiatanId) {
                $query->where('id', $kegiatanId);
            })->get();

            // Group by indikator to handle duplicates
            $groupedByIndikator = $matriksFraData->groupBy('indikator');

            foreach ($groupedByIndikator as $indikator => $matriksList) {
                // Get target value from the first matriks in the group
                $firstMatriks = $matriksList->first();
                $targetValue = $request->input('targets_pk' . $firstMatriks->id);

                // Apply the same target value to all matriks with the same indikator
                foreach ($matriksList as $matriks) {
                    TargetPK::updateOrCreate(
                        ['matriks_fra_id' => $matriks->id],
                        ['target_pk' => $targetValue]
                    );
                }
            }

            DB::commit();

            if ($request->input('action_type') === 'finalize') {
                // Handle finalization logic here
                return redirect()->route('manajemen.pk')->with('success', 'Target PK berhasil difinalisasi!');
            }

            return redirect()->back()->with('success', 'Target PK berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
