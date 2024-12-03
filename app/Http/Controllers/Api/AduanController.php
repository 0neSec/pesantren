<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AduanController extends Controller
{
    public function __construct()
    {
        // Role-based middleware similar to Mutabaah
        $this->middleware('role:1,2,3,4')->only(['index', 'show']);
        $this->middleware('role:2,3,4')->only(['store', 'update']);
        $this->middleware('role:4')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            $query = Aduan::with(['pelapor', 'jenisAduan']);

            if ($request->has('pelapor_id')) {
                $query->where('pelapor_id', $request->pelapor_id);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_waktu', [$request->start_date, $request->end_date]);
            }

            $aduan = $query->orderBy('tanggal_waktu', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Aduan records retrieved successfully',
                'data' => $aduan
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve aduan records: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving aduan records',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate initial required fields
            $validated = $request->validate([
                'tanggal_waktu' => 'required|date',
                'jenis_aduan_id' => 'required|exists:jenis_aduan,id',
                'alasan' => 'required|string',
                'keterangan' => 'required|string',
                'pelapor_id' => 'required|exists:users,id',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            // Manually handle boolean fields with more flexible validation
            $dalam_tekanan = $request->input('dalam_tekanan');
            $kesadaran_penuh = $request->input('kesadaran_penuh');

            // Convert input to boolean using PHP's filter_var
            if ($dalam_tekanan !== null) {
                $validated['dalam_tekanan'] = filter_var($dalam_tekanan, FILTER_VALIDATE_BOOLEAN);
            }

            if ($kesadaran_penuh !== null) {
                $validated['kesadaran_penuh'] = filter_var($kesadaran_penuh, FILTER_VALIDATE_BOOLEAN);
            }

            // Custom role validation
            $pelapor = User::findOrFail($validated['pelapor_id']);
            // Add specific role validation if needed
            // For example: if ($pelapor->role_id != 1) { ... }

            if ($request->hasFile('media')) {
                $path = $request->file('media')->store('aduan_media', 'public');
                $validated['media_path'] = $path;
            }

            $aduan = Aduan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Aduan record created successfully',
                'data' => $aduan->load(['pelapor', 'jenisAduan'])
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to create aduan record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating aduan record',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $aduan = Aduan::findOrFail($id);

            // Validate initial fields
            $validated = $request->validate([
                'tanggal_waktu' => 'sometimes|date',
                'jenis_aduan_id' => 'sometimes|exists:jenis_aduan,id',
                'alasan' => 'sometimes|string',
                'keterangan' => 'sometimes|string',
                'pelapor_id' => 'sometimes|exists:users,id',
                'media' => 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240'
            ]);

            // Manually handle boolean fields with more flexible validation
            $dalam_tekanan = $request->input('dalam_tekanan');
            $kesadaran_penuh = $request->input('kesadaran_penuh');

            // Convert input to boolean using PHP's filter_var
            if ($dalam_tekanan !== null) {
                $validated['dalam_tekanan'] = filter_var($dalam_tekanan, FILTER_VALIDATE_BOOLEAN);
            }

            if ($kesadaran_penuh !== null) {
                $validated['kesadaran_penuh'] = filter_var($kesadaran_penuh, FILTER_VALIDATE_BOOLEAN);
            }

            // Custom role validation for pelapor if updated
            if (isset($validated['pelapor_id'])) {
                $pelapor = User::findOrFail($validated['pelapor_id']);
                // Add specific role validation if needed
                // For example: if ($pelapor->role_id != 1) { ... }
            }

            if ($request->hasFile('media')) {
                if ($aduan->media_path) {
                    Storage::disk('public')->delete($aduan->media_path);
                }

                $path = $request->file('media')->store('aduan_media', 'public');
                $validated['media_path'] = $path;
            }

            $aduan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Aduan record updated successfully',
                'data' => $aduan->load(['pelapor', 'jenisAduan'])
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to update aduan record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating aduan record',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $aduan = Aduan::with(['pelapor', 'jenisAduan'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Aduan record retrieved successfully',
                'data' => $aduan
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve aduan record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Aduan record not found',
                'error' => 'Internal server error'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy($id)
    {
        try {
            $aduan = Aduan::findOrFail($id);

            // Delete associated media if exists
            if ($aduan->media_path) {
                Storage::disk('public')->delete($aduan->media_path);
            }

            $aduan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Aduan record deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to delete aduan record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting aduan record',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
