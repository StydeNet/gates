<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcceptTermsController extends Controller
{
    public function accept()
    {
        if (request()->has('accept')) {
            return back()->withCookie('accept_terms', '1');
        }

        return back();
    }
}
