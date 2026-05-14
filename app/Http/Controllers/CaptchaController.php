<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function generate()
    {
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
        $codigo = '';
        for ($i = 0; $i < 4; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        
        session(['captcha_code' => $codigo]);
        
        $colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];
        
        $html = '<div style="background: #2c3e50; padding: 12px; border-radius: 8px; text-align: center;">';
        for ($i = 0; $i < 4; $i++) {
            $color = $colors[array_rand($colors)];
            $rotate = rand(-10, 10);
            $html .= '<span style="
                display: inline-block;
                background: ' . $color . ';
                color: white;
                font-size: 28px;
                font-weight: bold;
                font-family: monospace;
                padding: 8px 12px;
                margin: 0 2px;
                border-radius: 5px;
                transform: rotate(' . $rotate . 'deg);
                box-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            ">' . $codigo[$i] . '</span>';
        }
        $html .= '</div>';
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'code' => $codigo
        ]);
    }
    
}