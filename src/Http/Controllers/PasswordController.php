<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\PasswordUpdateRequest as UpdateRequest;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class PasswordController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(): Response
    {
        return Inertia::render('Password', [
            'action' => URL::route('bazar.password.update'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\PasswordUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return Redirect::route('bazar.password.show')->with(
            'message', __('Your password has been updated.')
        );
    }
}
