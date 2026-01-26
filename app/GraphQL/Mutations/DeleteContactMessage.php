<?php

namespace App\GraphQL\Mutations;

use App\Models\ContactMessage;

class DeleteContactMessage
{
    /**
     * Delete a contact message.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $contactMessage = ContactMessage::findOrFail($args['id']);
        $contactMessage->delete();

        return [
            'success' => true,
            'message' => 'Contact message deleted successfully.',
        ];
    }
}
