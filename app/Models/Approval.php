<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Approval",
 *     required={"id", "expense_id", "approver_id", "status_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="expense_id", type="integer"),
 *     @OA\Property(property="approver_id", type="integer"),
 *     @OA\Property(property="status_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Approval extends Model
{
    use HasFactory;

    protected $fillable = ['expense_id', 'approver_id', 'status_id'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function approver()
    {
        return $this->belongsTo(Approver::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
