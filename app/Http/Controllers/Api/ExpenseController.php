<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\ApprovalStage;
use App\Models\Expense;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Expenses",
 *     description="API untuk mengelola Expenses (pengeluaran)"
 * )
 */
class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/expenses",
     *     tags={"Expenses"},
     *     @OA\Response(response=200, description="List of expenses")
     * )
     */
    public function index()
    {
        return Expense::with('status')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/expenses",
     *     tags={"Expenses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "status_id"},
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="status_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'amount' => 'required|numeric',
        ]);

        // Simpan expense dengan status "menunggu persetujuan" (status_id = 1)
        $expense = Expense::create([
            'amount' => $validated['amount'],
            'status_id' => 1
        ]);

        // Tambahkan ke tabel approvals
        Approval::create([
            'expense_id' => $expense->id,
            'approver_id' => null,
            'status_id' => 1 // menunggu persetujuan
        ]);

        return response()->json([
            'message' => 'Expense dan tahapan approval berhasil dibuat.',
            'data' => $expense
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/expenses/{id}",
     *     tags={"Expenses"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Expense detail")
     * )
     */
    public function show($id)
    {
        return Expense::with('status')->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/expenses/{id}",
     *     tags={"Expenses"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(@OA\Property(property="amount", type="number", format="float"), @OA\Property(property="status_id", type="integer"))
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $data = $request->validate([
            'amount' => 'required|numeric',
            'status_id' => 'required|exists:statuses,id',
        ]);
        $expense->update($data);

        return $expense;
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/expenses/{id}",
     *     tags={"Expenses"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->noContent();
    }
}
