<?php

namespace App\Admin\Services;

use App\Admin\Events\UpdateAdminChatEvent;
use App\Admin\Jobs\BanEmail;
use App\Flare\Events\ServerMessageEvent;
use App\Flare\Jobs\UpdateSilencedUserJob;
use App\Flare\Models\User;
use App\Admin\Events\RefreshUserScreenEvent;
use App\Admin\Jobs\UpdateBannedUserJob;
use App\Game\Core\Events\UpdateTopBarEvent;
use App\Game\Messages\Events\GlobalMessageEvent;


class UserService {

    public function banUser(User $user, array $params) {
        $unBanAt = null;

        if ($params['for'] !== 'perm') {
            $unBanAt = $this->fetchUnBanAt($user, $params['for']);

            if (is_null($unBanAt)) {
                return false;
            }
        } else {
            $this->broadCastAdminMessage($user);
        }

        $user->update([
            'is_banned'     => true,
            'unbanned_at'   => $unBanAt,
            'banned_reason' => $params['reason'],
        ]);

        $user = $user->refresh();

        event(new UpdateTopBarEvent($user->character));

        $this->sendUserMail($user, $unBanAt);

        return true;
    }

    /**
     * Fetch the unban at value.
     *
     * We also schedule a job to unban them at a specific period of time.
     *
     * `$for` can be `one-day` or `one-week`
     *
     * @param User $user
     * @param string $for
     * @return mixed null | Carbon
     */
    public function fetchUnBanAt(User $user, string $for) {
        $unBanAt = null;

        switch($for) {
            case 'one-day':
                $unBanAt = now()->addDays(1);
                UpdateBannedUserJob::dispatch($user)->delay($unBanAt);
                break;
            case 'one-week':
                $unBanAt = now()->addWeeks(1);
                UpdateBannedUserJob::dispatch($user)->delay($unBanAt);
                break;
        }

        return $unBanAt;
    }

    /**
     * Silence the user.
     *
     * @param User $user
     * @param int $silenceFor
     */
    public function silence(User $user, int $silenceFor) {
        $canSpeakAgainAt = now()->addMinutes($silenceFor);

        $user->update([
            'is_silenced' => true,
            'can_speak_again_at' => $canSpeakAgainAt,
        ]);

        $user   = $user->refresh();

        $message = 'The creator has silenced you until: ' . $canSpeakAgainAt->format('Y-m-d H:i:s') . ' ('.(int) $silenceFor.' Minutes server time) Making accounts to get around this is a bannable offense.';

        event(new ServerMessageEvent($user, 'silenced', $message));

        event(new UpdateTopBarEvent($user->character));

        UpdateSilencedUserJob::dispatch($user)->delay($canSpeakAgainAt);

        broadcast(new UpdateAdminChatEvent(auth()->user()));
    }

    public function forceNameChange(User $user) {
        $user->character->update([
            'force_name_change' => true
        ]);

        event(new UpdateTopBarEvent($user->character->refresh()));

        broadcast(new UpdateAdminChatEvent(auth()->user()));
    }

    /**
     * When a user gets banned perm we broad cast a message for all to see.
     *
     * @param User $user
     * @return void
     */
    public function broadCastAdminMessage(User $user): void {
        $message = $user->character->name . ' Sees the sky open and lightening comes hurtling down, striking the earth - cracking the air for miles around! They have been smitten by the hand of The Creator!';

        event(new GlobalMessageEvent($message));
    }

    /**
     * Send the banned mail to the user.
     *
     * This alerts the user they have been banned.
     *
     * @param user $user
     * @param Carbon | null $unBanAt
     * @return void
     */
    public function sendUserMail(User $user, $unBanAt): void {
        event(new RefreshUserScreenEvent($user));

        $unBannedAt = !is_null($unBanAt) ? $unBanAt->format('l jS \\of F Y h:i:s A') . ' ' . $unBanAt->timezoneName . '.' : 'Forever.';
        $message    = 'You have been banned until: ' . $unBannedAt . ' For the reason of: ' . $user->banned_reason;

        BanEmail::dispatch($user, $message)->delay(now()->addMinutes(1));
    }
}
