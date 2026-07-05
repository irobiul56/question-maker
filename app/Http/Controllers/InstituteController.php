<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InstituteController extends Controller
{
    public function institute() {
        return Inertia::render('Institute/Dashboard', [

        ]);
    }
}
