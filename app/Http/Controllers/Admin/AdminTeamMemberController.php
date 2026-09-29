<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminTeamMemberController extends Controller
{
    /**
     * Store a newly created team member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'role_title'  => ['required', 'string', 'max:255'],
            'department'  => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'telegram'    => ['nullable', 'string', 'max:100'],
            'linkedin'    => ['nullable', 'string', 'max:255'],
            'github'      => ['nullable', 'string', 'max:255'],
            'bio'         => ['nullable', 'string', 'max:1000'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'joined_date' => ['nullable', 'date'],
            'status'      => ['required', 'in:active,inactive'],
            'avatar_file' => ['nullable', 'image', 'max:4096'],
            'avatar_url'  => ['nullable', 'url', 'max:500'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar_file')) {
            $avatarPath = $request->file('avatar_file')->store('team-avatars', 'public');
        } elseif (!empty($validated['avatar_url'])) {
            $avatarPath = $validated['avatar_url'];
        }

        $teamMember = TeamMember::create([
            'uuid'        => (string) Str::uuid(),
            'name'        => $validated['name'],
            'role_title'  => $validated['role_title'],
            'department'  => $validated['department'] ?? 'تیم اصلی',
            'email'       => $validated['email'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'telegram'    => $validated['telegram'] ? ltrim($validated['telegram'], '@') : null,
            'linkedin'    => $validated['linkedin'] ?? null,
            'github'      => $validated['github'] ?? null,
            'bio'         => $validated['bio'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
            'joined_date' => $validated['joined_date'] ?? now()->toDateString(),
            'status'      => $validated['status'],
            'avatar'      => $avatarPath,
            'order'       => 0,
        ]);

        return back()->with('success', "هم‌تیمی جدید «{$teamMember->name}» با موفقیت اضافه شد و کیو‌آر کد اختصاصی صادر گردید.");
    }

    /**
     * Update the specified team member.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'role_title'  => ['required', 'string', 'max:255'],
            'department'  => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'telegram'    => ['nullable', 'string', 'max:100'],
            'linkedin'    => ['nullable', 'string', 'max:255'],
            'github'      => ['nullable', 'string', 'max:255'],
            'bio'         => ['nullable', 'string', 'max:1000'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'joined_date' => ['nullable', 'date'],
            'status'      => ['required', 'in:active,inactive'],
            'avatar_file' => ['nullable', 'image', 'max:4096'],
            'avatar_url'  => ['nullable', 'url', 'max:500'],
        ]);

        $dataToUpdate = [
            'name'        => $validated['name'],
            'role_title'  => $validated['role_title'],
            'department'  => $validated['department'] ?? 'تیم اصلی',
            'email'       => $validated['email'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'telegram'    => $validated['telegram'] ? ltrim($validated['telegram'], '@') : null,
            'linkedin'    => $validated['linkedin'] ?? null,
            'github'      => $validated['github'] ?? null,
            'bio'         => $validated['bio'] ?? null,
            'employee_id' => $validated['employee_id'] ?? $teamMember->employee_id,
            'joined_date' => $validated['joined_date'] ?? $teamMember->joined_date,
            'status'      => $validated['status'],
        ];

        if ($request->hasFile('avatar_file')) {
            // Remove previous avatar if it was locally stored
            if ($teamMember->avatar && !str_starts_with($teamMember->avatar, 'http')) {
                Storage::disk('public')->delete($teamMember->avatar);
            }
            $dataToUpdate['avatar'] = $request->file('avatar_file')->store('team-avatars', 'public');
        } elseif (!empty($validated['avatar_url'])) {
            $dataToUpdate['avatar'] = $validated['avatar_url'];
        }

        $teamMember->update($dataToUpdate);

        return back()->with('success', "مشخصات هم‌تیمی «{$teamMember->name}» با موفقیت بروزرسانی شد.");
    }

    /**
     * Remove the specified team member.
     */
    public function destroy(TeamMember $teamMember)
    {
        $name = $teamMember->name;
        if ($teamMember->avatar && !str_starts_with($teamMember->avatar, 'http')) {
            Storage::disk('public')->delete($teamMember->avatar);
        }

        $teamMember->delete();

        return back()->with('success', "هم‌تیمی «{$name}» با موفقیت حذف گردید.");
    }

    /**
     * Download the QR code as SVG.
     */
    public function downloadQrSvg(TeamMember $teamMember)
    {
        $svg = QrCodeService::generateSvg($teamMember->public_url, 12);
        $filename = 'edvora-qr-' . Str::slug($teamMember->name ?: 'member') . '.svg';

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
