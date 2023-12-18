<?php

namespace App\Http\Controllers;

use App\Services\ShareService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __invoke(ShareService $service) {
        $names = $service->getNames();

        return view('admin', compact('names'));
    }
}
