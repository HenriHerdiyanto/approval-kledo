<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ApprovalStage;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="ApprovalStages",
 *     description="API untuk mengelola Approve By"
 * )
 */
class ApprovalStageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/approval-stages",
     *     tags={"ApprovalStages"},
     *     @OA\Response(response=200, description="List of approval stages")
     * )
     */
    public function index()
    {
        return ApprovalStage::with('approver')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/approval-stages",
     *     tags={"ApprovalStages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"approver_id"},
     *             @OA\Property(property="approver_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'approver_id' => 'required|exists:approvers,id',
        ]);

        return ApprovalStage::create($data);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/approval-stages/{id}",
     *     tags={"ApprovalStages"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Approval stage detail")
     * )
     */
    public function show($id)
    {
        return ApprovalStage::with('approver')->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/approval-stages/{id}",
     *     tags={"ApprovalStages"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(@OA\Property(property="approver_id", type="integer"))
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $stage = ApprovalStage::findOrFail($id);
        $data = $request->validate([
            'approver_id' => 'required|exists:approvers,id',
        ]);
        $stage->update($data);

        return $stage;
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/approval-stages/{id}",
     *     tags={"ApprovalStages"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $stage = ApprovalStage::findOrFail($id);
        $stage->delete();

        return response()->noContent();
    }
}
