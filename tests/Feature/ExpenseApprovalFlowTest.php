<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Approver;
use App\Models\ApprovalStage;
use App\Models\Expense;
use App\Models\Approval;
use App\Models\Status;

class ExpenseApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Paksa gunakan koneksi mysql saat test
        Config::set('database.default', 'mysql');
    }

    public function test_full_approval_flow()
    {
        // 1. Seed Statuses
        $statusPending = Status::create(['name' => 'menunggu persetujuan']);
        $statusApproved = Status::create(['name' => 'disetujui']);

        // 2. Buat 3 approvers
        $ana = Approver::create(['name' => 'Ana']);
        $ani = Approver::create(['name' => 'Ani']);
        $ina = Approver::create(['name' => 'Ina']);

        // 3. Buat approval stages (urut)
        ApprovalStage::create(['approver_id' => $ana->id]);
        ApprovalStage::create(['approver_id' => $ani->id]);
        ApprovalStage::create(['approver_id' => $ina->id]);

        // 4. Buat 4 pengeluaran
        $expenses = [];
        for ($i = 0; $i < 4; $i++) {
            $expenses[] = Expense::create([
                'amount' => 1000 * ($i + 1),
                'status_id' => $statusPending->id
            ]);

            Approval::create([
                'expense_id' => $expenses[$i]->id,
                'approver_id' => null,
                'status_id' => $statusPending->id
            ]);
        }

        // -------------------------
        // APPROVAL LOGIC SIMULASI
        // -------------------------

        // Expense 1: disetujui oleh Ana, Ani, Ina (semua)
        foreach ([$ana, $ani, $ina] as $approver) {
            $this->approveExpense($expenses[0], $approver);
        }

        // Expense 2: disetujui oleh Ana, Ani
        foreach ([$ana, $ani] as $approver) {
            $this->approveExpense($expenses[1], $approver);
        }

        // Expense 3: disetujui oleh Ana
        $this->approveExpense($expenses[2], $ana);

        // Expense 4: tidak disetujui siapa pun

        // -------------------------
        // ASSERT
        // -------------------------

        $this->assertEquals('disetujui', $expenses[0]->fresh()->status->name);
        $this->assertEquals('menunggu persetujuan', $expenses[1]->fresh()->status->name);
        $this->assertEquals('menunggu persetujuan', $expenses[2]->fresh()->status->name);
        $this->assertEquals('menunggu persetujuan', $expenses[3]->fresh()->status->name);
    }

    private function approveExpense(Expense $expense, Approver $approver)
    {
        $approval = Approval::where('expense_id', $expense->id)
            ->whereNull('approver_id')
            ->first();

        if (!$approval) return;

        $approval->update([
            'approver_id' => $approver->id,
            'status_id' => 2, // disetujui
        ]);

        // Cek apakah semua tahapan sudah dilalui
        $totalStages = ApprovalStage::count();
        $totalApproved = Approval::where('expense_id', $expense->id)
            ->whereNotNull('approver_id')
            ->count();

        if ($totalApproved == $totalStages) {
            $expense->update([
                'status_id' => 2 // disetujui
            ]);
        } else {
            // Insert approval berikutnya
            Approval::create([
                'expense_id' => $expense->id,
                'approver_id' => null,
                'status_id' => 1 // pending
            ]);
        }
    }
}
