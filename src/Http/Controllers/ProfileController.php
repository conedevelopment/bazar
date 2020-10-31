<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\ProfileUpdateRequest as UpdateRequest;
use Bazar\Http\Response;
use Bazar\Support\Facades\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Bazar\Http\Response
     */
    public function show(): Response
    {
        return Component::render('Profile', [
            'action' => URL::route('bazar.profile.update'),
        ]);
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

        return Redirect::route('bazar.profile.show')->with(
            'message', __('Your profile has been updated.')
        );
    }
}
