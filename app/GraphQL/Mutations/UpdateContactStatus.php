<?php

namespace App\GraphQL\Mutations;

use App\Models\ContactMessage;

class UpdateContactStatus
{
    /**
     * Update the status of a contact message.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $contactMessage = ContactMessage::findOrFail($args['id']);

        $contactMessage->update([
            'status' => $args['status'],
        ]);

        return $contactMessage;
    }
}
