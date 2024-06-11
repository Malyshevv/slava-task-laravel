<?php

namespace App\Events\Birthday;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BirthdayEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	private mixed $record;

	/**
     * Create a new event instance.
     */
    public function __construct($record)
    {
	    $this->record = $record;
    }

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel[]
	 */
	public function broadcastOn(): array
	{
		return [
			new Channel('birthday')
		];
	}

	/**
	 * Get the data to broadcast.
	 *
	 * @return array
	 */
	public function broadcastWith(): array
	{
		return [
			'record' => $this->record,
		];
	}
}
