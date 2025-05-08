<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Expense",
 *     required={"id", "amount", "status_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="status_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'status_id'];
    protected $attributes = [
        'status_id' => 1,
    ];
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
