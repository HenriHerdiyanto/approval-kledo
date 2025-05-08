<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Expense;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Approvals")
 */
/**
 * @OA\Tag(
 *     name="Approvals",
 *     description="API untuk detail approval"
 * )
 */
class ApprovalController extends Controller
{
    public function approve(Request $request, $id)
    {
        $approval = Approval::findOrFail($id);

        // Cek apakah sudah disetujui sebelumnya
        if ($approval->status_id == 2) {
            return response()->json(['message' => 'Sudah disetujui sebelumnya.'], 400);
        }

        // Ambil semua approval terkait expense ini
        $allApprovals = Approval::where('expense_id', $approval->expense_id)->orderBy('id')->get();

        // Pastikan approval sebelumnya sudah disetujui
        foreach ($allApprovals as $step) {
            if ($step->id == $approval->id) {
                break; // giliran dia
            }

            if ($step->status_id != 2) {
                return response()->json(['message' => 'Approval sebelumnya belum disetujui.'], 403);
            }
        }

        // Setujui approval
        $approval->update([
            'status_id' => 2,
        ]);

        // Cek apakah semua approval sudah selesai
        $pending = Approval::where('expense_id', $approval->expense_id)
            ->where('status_id', '!=', 2)
            ->count();

        if ($pending == 0) {
            // Semua sudah approve, update status expense
            $approval->expense->update(['status_id' => 2]); // disetujui
        }

        return response()->json(['message' => 'Approval disetujui']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/approvals",
     *     tags={"Approvals"},
     *     @OA\Response(response=200, description="List of approvals")
     * )
     */
    public function index()
    {
        return Approval::with(['expense', 'approver', 'status'])->get();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/approvals",
     *     tags={"Approvals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"expense_id", "approver_id", "status_id"},
     *             @OA\Property(property="expense_id", type="integer"),
     *             @OA\Property(property="approver_id", type="integer"),
     *             @OA\Property(property="status_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'approver_id' => 'required|exists:approvers,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        return Approval::create($data);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/approvals/{id}",
     *     tags={"Approvals"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Approval detail")
     * )
     */
    public function show($id)
    {
        return Approval::with(['expense', 'approver', 'status'])->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/approvals/{id}",
     *     tags={"Approvals"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(@OA\Property(property="expense_id", type="integer"), @OA\Property(property="approver_id", type="integer"), @OA\Property(property="status_id", type="integer"))
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $approval = Approval::findOrFail($id);

        // Validasi input
        $data = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'approver_id' => 'required|exists:approvers,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $currentApproverId = $approval->approver_id;
        $newApproverId = $data['approver_id'];

        // Validasi urutan approver
        if ($currentApproverId !== null) {
            if ($currentApproverId >= $newApproverId) {
                return response()->json([
                    'message' => 'Proses approval harus mengikuti urutan yang benar.',
                    'error' => 'Approver ID tidak sesuai dengan urutan yang benar.'
                ], 400);
            }
        } else {
            if ($newApproverId != 1) {
                return response()->json([
                    'message' => 'Approver pertama harus 1.',
                    'error' => 'Approver ID tidak valid.'
                ], 400);
            }
        }

        // Update data approval
        $approval->update($data);

        // Jika approver_id = 3, update status_id pada tabel expenses menjadi 2 ("disetujui")
        if ($newApproverId == 3) {
            $expense = Expense::find($data['expense_id']);
            if ($expense) {
                $expense->update([
                    'status_id' => 2
                ]);
            }
            $approval = Approval::findOrFail($id);
            if ($approval) {
                $approval->update([
                    'status_id' => 2
                ]);
            }
        }

        return response()->json([
            'message' => 'Approval berhasil diperbarui.',
            'data' => $approval
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/approvals/{id}",
     *     tags={"Approvals"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $approval = Approval::findOrFail($id);
        $approval->delete();

        return response()->noContent();
    }
}
