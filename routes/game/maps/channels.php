<?php

// When the timeout for movement should show
Broadcast::channel('show-timeout-move-{userId}', function ($user, $userId) {
    return $user->id === (int) $userId;
});

// When updating the map for a user.
Broadcast::channel('update-map-{userId}', function($user, $userId) {
	return $user->id === (int) $userId;
});

// When we update the monsters list based on location.
Broadcast::channel('update-monsters-list-{userId}', function($user, $userId) {
    return $user->id === (int) $userId;
});

// When a user is traveling to another plane.
Broadcast::channel('update-plane-{userId}', function($user, $userId) {
    return $user->id === (int) $userId;
});

// When player moves and the duel button should update.
broadCast::channel('update-duel', function($user) {
    return $user;
});

// When the plane count of characters changes.
Broadcast::channel('global-character-count-plane', function($user) {
    return $user;
});

// When a kingdom is settled.
BroadCast::channel('global-map-update', function($user) {
   return $user;
});

// When the NPC Kingdoms update.
Broadcast::channel('npc-kingdoms-update', function($user) {
    return $user;
});

// When a enemy kingdoms morale gets updated.
broadCast::channel('enemy-kingdom-morale-update', function($user) {
    return $user;
});

// When we need to update crafting options based on location
broadCast::channel('update-location-base-crafting-options-{userId}', function($user, $userId) {
    return $user->id === (int) $userId;
});

// When we need to update special locations
broadCast::channel('update-location-base-shops-{userId}', function($user, $userId) {
    return $user->id === (int) $userId;
});

// Update the character base position
broadCast::channel('update-character-position-{userId}', function($user, $userId) {
    return $user->id === (int) $userId;
});

// Update the character fight section to show rank selection
broadCast::channel('update-rank-fight-{userId}', function($user, $userId) {
    return $user->id === (int) $userId;
});
