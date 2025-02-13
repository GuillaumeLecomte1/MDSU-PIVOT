<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Client/Orders/Index', [
            'orders' => []
        ]);
    }

    public function show($order)
    {
        return Inertia::render('Client/Orders/Show', [
            'order' => null
        ]);
    }
}
