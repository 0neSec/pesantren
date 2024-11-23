<?php

namespace App\Http\Controllers\Api\kelas;

use App\Http\Controllers\Controller;
use App\Models\kelas\Kelas;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KelasController extends Controller
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
            $kelas = Kelas::all();

            if ($kelas->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No classes found',
                    'data' => []
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Classes retrieved successfully',
                'data' => $kelas
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve classes',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
                'deskripsi' => 'nullable|string'
            ]);

            $kelas = Kelas::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Class created successfully',
                'data' => $kelas
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
                'message' => 'Failed to create class',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Class retrieved successfully',
                'data' => $kelas
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve class',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $validated = $request->validate([
                'nama_kelas' => 'sometimes|string|max:255|unique:kelas,nama_kelas,' . $id,
                'deskripsi' => 'nullable|string'
            ]);

            $kelas->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully',
                'data' => $kelas
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found',
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
                'message' => 'Failed to update class',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->delete();

            return response()->json([
                'success' => true,
                'message' => 'Class deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete class',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
