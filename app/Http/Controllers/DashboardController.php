<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return "No hay token. Token recibido: " . json_encode($request->all()) . "<br><a href='/'>Volver</a>";
        }
        
        return "<h1>Token recibido!</h1><p>Token: " . $token . "</p><a href='/'>Volver</a>";
    }
}