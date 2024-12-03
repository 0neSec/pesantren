<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisAduan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JenisAduanController extends Controller
{
    public function __construct()
    {
        // Apply role-based middleware similar to the previous controller
        $this->middleware('role:1,2,3,4')->only(['index', 'show']);
        $this->middleware('role:2,3,4')->only(['store', 'update']);
        $this->middleware('role:4')->only('destroy');
    }

    public function index()
    {
        try {
            $jenisAduan = JenisAduan::all();

            if ($jenisAduan->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No complaint types found',
                    'data' => []
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Complaint types retrieved successfully',
                'data' => $jenisAduan
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve complaint types',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:jenis_aduan,nama',
                'deskripsi' => 'nullable|string'
            ]);

            $jenisAduan = JenisAduan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Complaint type created successfully',
                'data' => $jenisAduan
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create complaint type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $jenisAduan = JenisAduan::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Complaint type retrieved successfully',
                'data' => $jenisAduan
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve complaint type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $jenisAduan = JenisAduan::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'sometimes|string|max:255|unique:jenis_aduan,nama,' . $id,
                'deskripsi' => 'nullable|string'
            ]);

            $jenisAduan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Complaint type updated successfully',
                'data' => $jenisAduan
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update complaint type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $jenisAduan = JenisAduan::findOrFail($id);
            $jenisAduan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Complaint type deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete complaint type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
