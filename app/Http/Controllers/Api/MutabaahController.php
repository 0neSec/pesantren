<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mutabaah;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MutabaahController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:1,2,3,4')->only(['index', 'show']);
        $this->middleware('role:2,3,4')->only(['store', 'update']);
        $this->middleware('role:4')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            $query = Mutabaah::with(['santri', 'kelas', 'ustadz', 'jenisSetoran', 'kitabSurah']);

            // Filter by santri_id if provided
            if ($request->has('santri_id')) {
                $query->where('santri_id', $request->santri_id);
            }

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('waktu_mulai', [$request->start_date, $request->end_date]);
            }

            $mutabaah = $query->orderBy('waktu_mulai', 'desc')->get();

            if ($mutabaah->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No mutabaah records found',
                    'data' => []
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mutabaah records retrieved successfully',
                'data' => $mutabaah
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve mutabaah records: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving mutabaah records',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'santri_id' => 'required|exists:users,id',
                'kelas_id' => 'required|exists:kelas,id',
                'ustadz_id' => 'required|exists:users,id',
                'jenis_storan_id' => 'required|exists:jenis_setoran,id',
                'kitab_surah_id' => 'required|exists:kitab_surah,id',
                'waktu_mulai' => 'required|date',
                'waktu_selesai' => 'required|date|after:waktu_mulai',
                'mulai_storan' => 'required|string',
                'akhir_storan' => 'required|string',
                'nilai_bacaan' => 'required|numeric|between:0,100',
                'nilai_hafalan' => 'required|numeric|between:0,100',
                'kendala' => 'nullable|string',
                'deskripsi' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            if ($request->hasFile('media')) {
                $path = $request->file('media')->store('mutabaah_media', 'public');
                $validated['media_path'] = $path;
            }

            $mutabaah = Mutabaah::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Mutabaah record created successfully',
                'data' => $mutabaah->load(['santri', 'kelas', 'ustadz', 'jenisSetoran', 'kitabSurah'])
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to create mutabaah record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating mutabaah record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $mutabaah = Mutabaah::with(['santri', 'kelas', 'ustadz', 'jenisSetoran', 'kitabSurah'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Mutabaah record retrieved successfully',
                'data' => $mutabaah
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mutabaah record not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve mutabaah record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving mutabaah record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $mutabaah = Mutabaah::findOrFail($id);

            $validated = $request->validate([
                'santri_id' => 'sometimes|exists:users,id',
                'kelas_id' => 'sometimes|exists:kelas,id',
                'ustadz_id' => 'sometimes|exists:users,id',
                'jenis_storan_id' => 'sometimes|exists:jenis_setoran,id',
                'kitab_surah_id' => 'sometimes|exists:kitab_surah,id',
                'waktu_mulai' => 'sometimes|date',
                'waktu_selesai' => 'sometimes|date|after:waktu_mulai',
                'mulai_storan' => 'sometimes|string',
                'akhir_storan' => 'sometimes|string',
                'nilai_bacaan' => 'sometimes|numeric|between:0,100',
                'nilai_hafalan' => 'sometimes|numeric|between:0,100',
                'kendala' => 'nullable|string',
                'deskripsi' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            if ($request->hasFile('media')) {
                // Delete old media if exists
                if ($mutabaah->media_path) {
                    Storage::disk('public')->delete($mutabaah->media_path);
                }

                $path = $request->file('media')->store('mutabaah_media', 'public');
                $validated['media_path'] = $path;
            }

            $mutabaah->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Mutabaah record updated successfully',
                'data' => $mutabaah->load(['santri', 'kelas', 'ustadz', 'jenisSetoran', 'kitabSurah'])
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mutabaah record not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to update mutabaah record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating mutabaah record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $mutabaah = Mutabaah::findOrFail($id);

            // Delete associated media file if exists
            if ($mutabaah->media_path) {
                Storage::disk('public')->delete($mutabaah->media_path);
            }

            $mutabaah->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mutabaah record deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mutabaah record not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Failed to delete mutabaah record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting mutabaah record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
