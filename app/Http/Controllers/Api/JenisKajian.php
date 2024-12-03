<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisKajian;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JenisKajianController extends Controller
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
            $jenisKajian = JenisKajian::all();

            if ($jenisKajian->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No study types found',
                    'data' => []
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Study types retrieved successfully',
                'data' => $jenisKajian
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve study types',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:jenis_kajian,nama',
                'deskripsi' => 'nullable|string'
            ]);

            $jenisKajian = JenisKajian::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Study type created successfully',
                'data' => $jenisKajian
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
                'message' => 'Failed to create study type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $jenisKajian = JenisKajian::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Study type retrieved successfully',
                'data' => $jenisKajian
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Study type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve study type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $jenisKajian = JenisKajian::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'sometimes|string|max:255|unique:jenis_kajian,nama,' . $id,
                'deskripsi' => 'nullable|string'
            ]);

            $jenisKajian->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Study type updated successfully',
                'data' => $jenisKajian
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Study type not found',
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
                'message' => 'Failed to update study type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $jenisKajian = JenisKajian::findOrFail($id);
            $jenisKajian->delete();

            return response()->json([
                'success' => true,
                'message' => 'Study type deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Study type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete study type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
