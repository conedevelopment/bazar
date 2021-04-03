<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\ProfileUpdateRequest as UpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(): Response
    {
        return Inertia::render('Profile');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $request->user()->update(
            $request->validated()
        );

        return Redirect::route('bazar.profile.show')
                        ->with('message', __('Your profile has been updated.'));
    }
}
