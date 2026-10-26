<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\AcceptInvitationRequest;
use App\Services\FacilityInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(string $token, FacilityInvitationService $invitations): View
    {
        try {
            $invitation = $invitations->findUsableInvitation($token);
        } catch (ValidationException $exception) {
            return view('invitations.invalid', [
                'message' => collect($exception->errors())->flatten()->first(),
            ]);
        }

        return view('invitations.show', [
            'invitation' => $invitation,
            'token' => $token,
            'roleLabel' => UserRole::tryFrom($invitation->role)?->label() ?? $invitation->role,
        ]);
    }

    public function accept(
        AcceptInvitationRequest $request,
        string $token,
        FacilityInvitationService $invitations,
    ): RedirectResponse {
        try {
            $user = $invitations->accept(
                $token,
                $request->string('name')->toString(),
                $request->string('password')->toString(),
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('status', __('invitations.accept.welcome', ['name' => $user->name]));
    }
}
