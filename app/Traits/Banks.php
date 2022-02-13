<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait Banks
{
    public function listBanks(Request $request)
    {
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
            ])->accept('application/json')->get('https://api.paystack.co/bank');
            return $request->wantsJson()
                ? response()->json($response->json())
                : $response->collect();
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json(["message" => $e->getMessage()], 400)
                : ["error" => $e->getMessage()];
        }
    }

    public function fetchAccountDetails(Request $request)
    {
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
            ])->accept('application/json')->get("https://api.paystack.co/bank/resolve?account_number=$request->account_number&bank_code=$request->bank_code");
            return $request->wantsJson()
                ? response()->json($response->json())
                : $response->collect();
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json(["message" => $e->getMessage()], 400)
                : ["error" => $e->getMessage()];
        }
    }
}