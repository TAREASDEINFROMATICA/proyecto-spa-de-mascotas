<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFAController extends Controller
{
    public function generar(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        $google2fa = new Google2FA();
        $secreto = $google2fa->generateSecretKey();
        
        session(['temp_2fa_secret' => $secreto]);
        
        $url = $google2fa->getQRCodeUrl('Pet Spa', $user->correo, $secreto);
        $qrCode = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($url);
        
        return response()->json([
            'success' => true,
            'secreto' => $secreto,
            'qr' => $qrCode
        ]);
    }
    
    public function verificar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|size:6',
            'secreto' => 'required|string'
        ]);
        
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        $google2fa = new Google2FA();
        
        if ($google2fa->verifyKey($request->secreto, $request->codigo)) {
            $user->two_factor_secret = $request->secreto;
            $user->save();
            
            return response()->json(['success' => true, 'message' => '2FA activado']);
        }
        
        return response()->json(['success' => false, 'message' => 'Código incorrecto']);
    }
    
    public function desactivar(Request $request)
    {
        $user = $request->user();
        $user->two_factor_secret = null;
        $user->save();
        
        return response()->json(['success' => true, 'message' => '2FA desactivado']);
    }
}