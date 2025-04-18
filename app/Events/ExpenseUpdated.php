<?php

namespace App\Events;

use App\Models\Expense;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpenseUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The updated Expense instance.
     *
     * @var \App\Models\Expense
     */
    public Expense $expense;
    /**
     * The original attributes of the expense before the update. (Includes the values of the attributes)
     *
     * @var array
     */
    public array $originalAttributes;
    /**
     * The attributes that were changed. (Doesn't contain the values of the attributes)
     *
     * @var array
     */
    public array $changedAttributes;

    /**
     * Create a new event instance.
     */
    public function __construct(Expense $expense, array $originalAttributes, array $changedAttributes)
    {
        $this->expense = $expense;
        $this->originalAttributes = $originalAttributes;
        $this->changedAttributes = $changedAttributes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [];
    }
}