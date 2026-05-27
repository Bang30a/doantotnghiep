<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phien dang nhap da het han.',
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Phien dang nhap da het han, vui long dang nhap lai.');
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phien dang nhap da het han.',
                        'redirect' => route('login'),
                    ], 419);
                }

                return redirect()
                    ->route('login')
                    ->with('error', 'Phien dang nhap da het han, vui long dang nhap lai.');
            }

            return null;
        });
    })
    ->create();