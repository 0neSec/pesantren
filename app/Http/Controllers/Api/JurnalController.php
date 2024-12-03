<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    public function __construct()
    {
        // Role-based middleware
        $this->middleware('role:1,2,3,4')->only(['index', 'show']);
        $this->middleware('role:2,3,4')->only(['store', 'update']);
        $this->middleware('role:4')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            $query = Jurnal::with(['kelas', 'pelapor']);

            // Filter by kelas
            if ($request->has('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }

            // Filter by jenis_temuan
            if ($request->has('jenis_temuan')) {
                $query->where('jenis_temuan', $request->jenis_temuan);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            }

            // Filter by nama_santri
            if ($request->has('nama_santri')) {
                $query->where('nama_santri', 'like', '%' . $request->nama_santri . '%');
            }

            $jurnal = $query->orderBy('tanggal', 'desc')
                            ->orderBy('waktu', 'desc')
                            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Jurnal records retrieved successfully',
                'data' => $jurnal
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve jurnal records: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving jurnal records',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'waktu' => 'required|date_format:H:i',
                'nama_santri' => 'required|string|max:255',
                'kelas_id' => 'required|exists:kelas,id',
                'temuan_perilaku' => 'required|string',
                'jenis_temuan' => 'required|in:positif,negatif',
                'pelapor_id' => 'required|exists:users,id',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            // Custom role validation
            $pelapor = User::findOrFail($validated['pelapor_id']);
            // Add specific role validation if needed

            // Handle media upload
            if ($request->hasFile('media')) {
                $path = $request->file('media')->store('jurnal_media', 'public');
                $validated['media_path'] = $path;
            }

            // Remove media from validated to match model fillable
            unset($validated['media']);

            $jurnal = Jurnal::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jurnal record created successfully',
                'data' => $jurnal->load(['kelas', 'pelapor'])
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to create jurnal record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating jurnal record',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $jurnal = Jurnal::findOrFail($id);

            $validated = $request->validate([
                'tanggal' => 'sometimes|date',
                'waktu' => 'sometimes|date_format:H:i',
                'nama_santri' => 'sometimes|string|max:255',
                'kelas_id' => 'sometimes|exists:kelas,id',
                'temuan_perilaku' => 'sometimes|string',
                'jenis_temuan' => 'sometimes|in:positif,negatif',
                'pelapor_id' => 'sometimes|exists:users,id',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            // Handle media upload
            if ($request->hasFile('media')) {
                // Delete old media if exists
                if ($jurnal->media_path) {
                    Storage::disk('public')->delete($jurnal->media_path);
                }

                $path = $request->file('media')->store('jurnal_media', 'public');
                $validated['media_path'] = $path;
            }

            // Remove media from validated to match model fillable
            unset($validated['media']);

            $jurnal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jurnal record updated successfully',
                'data' => $jurnal->load(['kelas', 'pelapor'])
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to update jurnal record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating jurnal record',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $jurnal = Jurnal::with(['kelas', 'pelapor'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Jurnal record retrieved successfully',
                'data' => $jurnal
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve jurnal record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Jurnal record not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy($id)
    {
        try {
            $jurnal = Jurnal::findOrFail($id);

            // Delete associated media if exists
            if ($jurnal->media_path) {
                Storage::disk('public')->delete($jurnal->media_path);
            }

            $jurnal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jurnal record deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to delete jurnal record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting jurnal record',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
