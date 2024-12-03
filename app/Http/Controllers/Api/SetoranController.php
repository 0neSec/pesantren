<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisSetoran;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SetoranController extends Controller
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
            $jenisSetoran = JenisSetoran::all();

            if ($jenisSetoran->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No storage types found',
                    'data' => []
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Storage types retrieved successfully',
                'data' => $jenisSetoran
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve storage types',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:jenis_setoran,nama',
                'deskripsi' => 'nullable|string'
            ]);

            $jenisSetoran = JenisSetoran::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Storage type created successfully',
                'data' => $jenisSetoran
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
                'message' => 'Failed to create storage type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $jenisSetoran = JenisSetoran::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Storage type retrieved successfully',
                'data' => $jenisSetoran
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Storage type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve storage type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $jenisSetoran = JenisSetoran::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'sometimes|string|max:255|unique:jenis_setoran,nama,' . $id,
                'deskripsi' => 'nullable|string'
            ]);

            $jenisSetoran->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Storage type updated successfully',
                'data' => $jenisSetoran
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Storage type not found',
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
                'message' => 'Failed to update storage type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $jenisSetoran = JenisSetoran::findOrFail($id);
            $jenisSetoran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Storage type deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Storage type not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete storage type',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
