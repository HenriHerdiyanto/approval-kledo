<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ApprovalStage",
 *     required={"id", "approver_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="approver_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ApprovalStage extends Model
{
    use HasFactory;

    protected $fillable = ['approver_id'];

    public function approver()
    {
        return $this->belongsTo(Approver::class);
    }
}
