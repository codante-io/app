<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomTestController extends Controller
{
    public function handle()
    {
        $user = Auth::user();
        $user->subscribeToPlan(1);
    }
}
