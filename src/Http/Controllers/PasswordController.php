<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\PasswordUpdateRequest as UpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(): Response
    {
        return Inertia::render('Password');
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

        return Redirect::route('bazar.password.show')
                        ->with('message', __('Your password has been updated.'));
    }
}
