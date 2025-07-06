<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactUsController extends Controller
{
    public function contactus() {

        return Inertia::render('UserDashboard/Contact');
    }

    public function opinion() {

        return Inertia::render('UserDashboard/Opinion');
    }


    
}
