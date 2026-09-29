<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'role_title',
        'department',
        'email',
        'phone',
        'telegram',
        'linkedin',
        'github',
        'bio',
        'avatar',
        'employee_id',
        'joined_date',
        'status',
        'order',
    ];

    protected $casts = [
        'joined_date' => 'date',
        'order' => 'integer',
    ];

    protected $appends = [
        'avatar_url',
        'public_url',
        'vcard_url',
        'qr_data_uri',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->employee_id)) {
                $maxId = static::max('id') ?? 0;
                $model->employee_id = 'EDV-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset('storage/' . ltrim($this->avatar, '/'));
        }

        // Return a sleek default avatar with the person's name
        $nameEncoded = urlencode($this->name ?: 'Member');
        return "https://ui-avatars.com/api/?name={$nameEncoded}&background=0284c7&color=ffffff&bold=true&size=256";
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/team/member/' . $this->uuid);
    }

    public function getVcardUrlAttribute(): string
    {
        return url('/team/member/' . $this->uuid . '/vcard');
    }

    public function generateVcard(): string
    {
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN;CHARSET=UTF-8:" . $this->name . "\r\n";
        $vcard .= "N;CHARSET=UTF-8:;" . $this->name . ";;;\r\n";
        $vcard .= "ORG;CHARSET=UTF-8:Edvora Tech (سامانه آموزشی ادورا)\r\n";
        if ($this->role_title) {
            $vcard .= "TITLE;CHARSET=UTF-8:" . $this->role_title . "\r\n";
        }
        if ($this->department) {
            $vcard .= "ROLE;CHARSET=UTF-8:" . $this->department . "\r\n";
        }
        if ($this->email) {
            $vcard .= "EMAIL;TYPE=INTERNET,PREF:" . $this->email . "\r\n";
        }
        if ($this->phone) {
            $cleanPhone = preg_replace('/[^0-9+]/', '', $this->phone);
            $vcard .= "TEL;TYPE=CELL,VOICE,PREF:" . $cleanPhone . "\r\n";
        }
        $vcard .= "URL:" . $this->public_url . "\r\n";
        if ($this->bio) {
            $cleanBio = str_replace(["\r", "\n"], ' ', strip_tags($this->bio));
            $vcard .= "NOTE;CHARSET=UTF-8:" . $cleanBio . "\r\n";
        }
        $vcard .= "REV:" . gmdate('Ymd\THis\Z') . "\r\n";
        $vcard .= "END:VCARD\r\n";

        return $vcard;
    }

    public function getQrDataUriAttribute(): string
    {
        return \App\Services\QrCodeService::generateDataUri($this->public_url, 8);
    }

    public function getQrSvgAttribute(): string
    {
        return \App\Services\QrCodeService::generateSvg($this->public_url, 8);
    }
}
