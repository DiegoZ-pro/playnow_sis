<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Página Acerca de
     */
    public function about()
    {
        return view('shop.pages.about');
    }

    /**
     * Página de Contacto
     */
    public function contact()
    {
        return view('shop.pages.contact');
    }

    /**
     * Procesar formulario de contacto
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Aquí podrías enviar un email (opcional por ahora)
        // Mail::to('ventas@playnow.com')->send(new ContactMessage($validated));

        return back()->with('success', '¡Mensaje enviado exitosamente! Te responderemos pronto.');
    }

    /**
     * Términos y Condiciones
     */
    public function terms()
    {
        return view('shop.pages.terms');
    }

    /**
     * Política de Privacidad
     */
    public function privacy()
    {
        return view('shop.pages.privacy');
    }
}