<?php

namespace App\Events;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Workshop;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCompletedLesson
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $lesson;
    public $workshop;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Workshop $workshop, Lesson $lesson)
    {
        $this->user = $user;
        $this->workshop = $workshop;
        $this->lesson = $lesson;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('channel-name')];
    }
}
