<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Assignment
 *
 * @property-read \App\User $assignedBy
 * @property-read \App\User $assignee
 * @property-read \App\Order $order
 * @mixin \Eloquent
 * @property int $id
 * @property int $order_id
 * @property int $assignee_id
 * @property int $has_comments
 * @property int $assigned_by
 * @property int $is_all_good
 * @property int $is_approved
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereHasComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereIsAllGood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Assignment whereUpdatedAt($value)
 */
class Assignment extends Model
{
    //
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee()
    {
        return $this->belongsTo('App\User', 'assignee_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedBy()
    {
        return $this->belongsTo('App\User', 'assigned_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
