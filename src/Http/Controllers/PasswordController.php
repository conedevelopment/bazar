<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Component;
use Bazar\Http\Requests\PasswordUpdateRequest as UpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class PasswordController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Bazar\Http\Component
     */
    public function show(): Component
    {
        return Response::component('bazar::password', [
            'passwords' => [
                'password' => null,
                'current_password' => null,
                'password_confirmation' => null,
            ],
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
