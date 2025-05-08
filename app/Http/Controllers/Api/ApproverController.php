<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalStage;
use App\Models\Approver;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Approvers",
 *     description="API untuk mengelola approvers"
 * )
 */
class ApproverController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/approvers",
     *     tags={"Approvers"},
     *     summary="Ambil semua approver",
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function index()
    {
        return Approver::all();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/approvers",
     *     tags={"Approvers"},
     *     summary="Tambah approver",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Berhasil dibuat")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string'
        ]);

        // Simpan approver baru
        $approver = Approver::create($data);

        // Tambahkan ke tabel approval_stages
        ApprovalStage::create([
            'approver_id' => $approver->id
        ]);

        return response()->json([
            'message' => 'Approver dan Approval Stage berhasil ditambahkan.',
            'data' => $approver
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/approvers/{id}",
     *     tags={"Approvers"},
     *     summary="Detail approver",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function show($id)
    {
        return Approver::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/approvers/{id}",
     *     tags={"Approvers"},
     *     summary="Update approver",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Berhasil diupdate")
     * )
     */
    public function update(Request $request, $id)
    {
        $approver = Approver::findOrFail($id);
        $approver->update($request->validate(['name' => 'required|string']));
        return $approver;
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/approvers/{id}",
     *     tags={"Approvers"},
     *     summary="Hapus approver",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Berhasil dihapus")
     * )
     */
    public function destroy($id)
    {
        $approver = Approver::findOrFail($id);
        $approver->delete();
        return response()->json(null, 204);
    }
}
