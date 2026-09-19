<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'status',
        'failure_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        Request $request,
        string $email,
        string $status = 'success',
        ?User $user = null,
        ?string $reason = null
    ): static {
        $userAgent = $request->userAgent() ?? '';

        // Detect Device
        $device = 'Desktop';
        if (preg_match('/(mobile|android|iphone|ipad|phone|ipod)/i', $userAgent)) {
            $device = preg_match('/(ipad|tablet)/i', $userAgent) ? 'Tablet' : 'Mobile';
        }

        // Detect Browser
        $browser = 'Other';
        if (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        }

        return static::create([
            'user_id'        => $user?->id,
            'email'          => $email,
            'ip_address'     => $request->ip(),
            'user_agent'     => substr($userAgent, 0, 500),
            'device'         => $device,
            'browser'        => $browser,
            'status'         => $status,
            'failure_reason' => $reason,
        ]);
    }
}
