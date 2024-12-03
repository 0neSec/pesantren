<?php

namespace App\Http\Controllers\Api\kajian;

use App\Http\Controllers\Controller;
use App\Models\Kajian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KajianController extends Controller
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
            $query = Kajian::with(['santri', 'kelas', 'jenisKajian', 'pelapor']);

            if ($request->has('santri_id')) {
                $query->where('santri_id', $request->santri_id);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_waktu', [$request->start_date, $request->end_date]);
            }

            $kajian = $query->orderBy('tanggal_waktu', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Kajian records retrieved successfully',
                'data' => $kajian
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve kajian records: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving kajian records',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal_waktu' => 'required|date',
                'santri_id' => 'required|exists:users,id',
                'kelas_id' => 'required|exists:kelas,id',
                'jenis_kajian_id' => 'required|exists:jenis_kajian,id',
                'nama_ustadz' => 'required|string|max:255',
                'judul_kitab' => 'required|string|max:255',
                'pelapor_id' => 'required|exists:users,id',
                'catatan' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            // Custom role validation
            $santri = User::findOrFail($validated['santri_id']);
            $pelapor = User::findOrFail($validated['pelapor_id']);

            if ($santri->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided santri must have role 1'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($request->hasFile('media')) {
                $path = $request->file('media')->store('kajian_media', 'public');
                $validated['media_path'] = $path;
            }

            $kajian = Kajian::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kajian record created successfully',
                'data' => $kajian->load(['santri', 'kelas', 'jenisKajian', 'pelapor'])
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to create kajian record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating kajian record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kajian = Kajian::findOrFail($id);

            $validated = $request->validate([
                'tanggal_waktu' => 'sometimes|date',
                'santri_id' => 'sometimes|exists:users,id',
                'kelas_id' => 'sometimes|exists:kelas,id',
                'jenis_kajian_id' => 'sometimes|exists:jenis_kajian,id',
                'nama_ustadz' => 'sometimes|string|max:255',
                'judul_kitab' => 'sometimes|string|max:255',
                'pelapor_id' => 'sometimes|exists:users,id',
                'catatan' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            // Custom role validation
            if (isset($validated['santri_id'])) {
                $santri = User::findOrFail($validated['santri_id']);
                if ($santri->role_id != 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The provided santri must have role 1'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            if ($request->hasFile('media')) {
                if ($kajian->media_path) {
                    Storage::disk('public')->delete($kajian->media_path);
                }

                $path = $request->file('media')->store('kajian_media', 'public');
                $validated['media_path'] = $path;
            }

            $kajian->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kajian record updated successfully',
                'data' => $kajian->load(['santri', 'kelas', 'jenisKajian', 'pelapor'])
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to update kajian record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating kajian record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $kajian = Kajian::with(['santri', 'kelas', 'jenisKajian', 'pelapor'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Kajian record retrieved successfully',
                'data' => $kajian
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve kajian record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving kajian record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $kajian = Kajian::findOrFail($id);

            // Delete associated media file if exists
            if ($kajian->media_path) {
                Storage::disk('public')->delete($kajian->media_path);
            }

            $kajian->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kajian record deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to delete kajian record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting kajian record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
