<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicTeamMemberController extends Controller
{
    /**
     * Display the specified team member's public verification profile.
     * Only displays this individual's details when their QR code is scanned.
     */
    public function show(string $uuid)
    {
        $member = TeamMember::where('uuid', $uuid)
            ->orWhere('id', $uuid)
            ->firstOrFail();

        $qrSvg = QrCodeService::generateSvg($member->public_url, 10);
        $qrDataUri = QrCodeService::generateDataUri($member->public_url, 10);

        return view('team.member', [
            'member'    => $member,
            'qrSvg'     => $qrSvg,
            'qrDataUri' => $qrDataUri,
        ]);
    }

    /**
     * Download digital vCard (.vcf) for instant contact saving on smartphones.
     */
    public function downloadVcard(string $uuid)
    {
        $member = TeamMember::where('uuid', $uuid)
            ->orWhere('id', $uuid)
            ->firstOrFail();

        $vcardContent = $member->generateVcard();
        $safeName = Str::slug($member->name ?: 'edvora-member');
        $filename = "edvora-{$safeName}.vcf";

        return response($vcardContent, 200, [
            'Content-Type'        => 'text/vcard; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
