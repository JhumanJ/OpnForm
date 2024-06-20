<?php

namespace App\Models;

use App\Mail\UserInvitationEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserInvite extends Model
{
    use HasFactory;

    public const ADMIN_ROLE = 'admin';
    public const USER_ROLE = 'user';
    public const PENDING_STATUS = 'pending';
    public const ACCEPTED_STATUS = 'accepted';

    public function inviteUser(string $email, string $role, Workspace $workspace, Carbon $validUntil = null)
    {
        $this->email = $email;
        $this->workspace_id = $workspace->id;
        $this->role = $role;
        $this->valid_until = $validUntil ??  now()->addDays(7);
        $this->token = $this->generateUniqueToken();
        $this->save();
        $this->sendEmail();
    }

    public function getLink()
    {
        return front_url('/register?email=' . $this->email . '&invite_token=' . $this->token);
    }

    public function generateUniqueToken()
    {
        do {
            $token = Str::random(100);
        } while (UserInvite::where('token', $token)->exists());

        return $token;
    }

    public function hasExpired()
    {
        return Carbon::parse($this->valid_until)->isPast();
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function sendEmail()
    {
        Mail::to($this->email)->send(new UserInvitationEmail($this->workspace->name, $this->getLink()));
    }
}
