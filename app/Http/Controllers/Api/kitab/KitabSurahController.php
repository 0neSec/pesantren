<?php

namespace App\Http\Controllers\Api\kitab;

use App\Http\Controllers\Controller;
use App\Models\jenis\KitabSurah;
use App\Models\KitabSurah as ModelsKitabSurah;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class KitabSurahController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:1,2,3,4')->only(['index', 'show']);
        $this->middleware('role:2,3,4')->only(['store', 'update']);
        $this->middleware('role:4')->only('destroy');
    }

    public function index()
    {
        try {
            $kitabSurah = ModelsKitabSurah::with('jenisSetoran')->get();

            if ($kitabSurah->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No kitab/surah found',
                    'data' => []
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kitab/surah retrieved successfully',
                'data' => $kitabSurah
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve kitab/surah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving kitab/surah',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'jenis_setoran_id' => 'required|exists:jenis_setoran,id',
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string'
            ]);

            $kitabSurah = ModelsKitabSurah::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kitab/surah created successfully',
                'data' => $kitabSurah->load('jenisSetoran')
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to create kitab/surah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating kitab/surah',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $kitabSurah = ModelsKitabSurah::with('jenisSetoran')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Kitab/surah retrieved successfully',
                'data' => $kitabSurah
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kitab/surah not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve kitab/surah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving kitab/surah',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kitabSurah = ModelsKitabSurah::findOrFail($id);

            $validated = $request->validate([
                'jenis_setoran_id' => 'sometimes|exists:jenis_setoran,id',
                'nama' => 'sometimes|string|max:255',
                'deskripsi' => 'nullable|string'
            ]);

            $kitabSurah->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kitab/surah updated successfully',
                'data' => $kitabSurah->load('jenisSetoran')
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kitab/surah not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Failed to update kitab/surah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating kitab/surah',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $kitabSurah = ModelsKitabSurah::findOrFail($id);
            $kitabSurah->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kitab/surah deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kitab/surah not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Failed to delete kitab/surah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting kitab/surah',
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
