<?php

namespace App\Firebase;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\Message;

class FirebaseCloudMessage
{
	private $messaging;

	public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification(Message $message)
    {
    	$this->messaging->send($message);
    }
}
