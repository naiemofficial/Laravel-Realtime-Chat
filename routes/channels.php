<?php

use App\Models\Guest;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.Guest.{id}', function ($guest, $id) {
    $guest = Guest::current();
    return $guest && ((int) $guest->id === (int) $id);
});
