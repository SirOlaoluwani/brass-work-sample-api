<?php

namespace App\Traits;

use App\Http\Requests\BulkTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

trait PaymentTrait
{
    /**
     * 
     */
    public function createTransferReceipient(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'account_number' => 'required|min:10',
                'bank_code' => 'required',
            ]);

            $validated["currency"] = "NGN";

            $response = Http::withHeaders([
                "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
            ])->accept('application/json')->post('https://api.paystack.co/transferrecipient', $validated);
            return $response->collect();
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json(["message" => $e->getMessage()], 400)
                : ["error" => $e->getMessage()];
        }
    }

    public function initiateTransfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required',
                'recipient' => 'required',
                'reason' => 'required',
            ]);

            $validated["source"] = "balance";

            $response = Http::withHeaders([
                "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
            ])->accept('application/json')->post('https://api.paystack.co/transfer', $validated);
            return $request->wantsJson()
                ? response()->json($response->json())
                : $response->collect();
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json(["message" => $e->getMessage()], 400)
                : ["error" => $e->getMessage()];
        }
    }

    public function initiateBulkTransfer(Request $request)
    {
        try {
            $initResponse = $this->makeBulkTransfer($request);
            return $request->wantsJson()
                ? response()->json($initResponse->json())
                : redirect()->back()->with("message", $initResponse["message"]);
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json(["message" => $e->getMessage()], 400)
                : ["error" => $e->getMessage()];
        }
    }

    public function makeBulkTransfer(BulkTransfer $request)
    {
        $input = $request->validated();

        $input["source"] = "balance";
        $input["currency"] = "NGN";

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
        ])->accept('application/json')->post('https://api.paystack.co/transfer/bulk', $input);

        return $response;
    }

    public function verifyTransaction($reference)
    {
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
        ])->accept('application/json')->get('https://api.paystack.co/transaction/verify/' . $reference);

        return $response;
    }

    public function chargeAuthorization($input)
    {
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
        ])->accept('application/json')->post('https://api.paystack.co/transaction/charge_authorization', $input);

        return $response;
    }

    public function listTransfers(Request $request)
    {
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
            ])->accept('application/json')->get("https://api.paystack.co/transfer?perPage=$request->perPage&page=$request->page");
            return $request->wantsJson()
                ? response()->json($response->json())
                : $response->collect();
        } catch (\Exception $e) {
            return $request->wantsJson()
                ? response()->json(["message" => $e->getMessage()], 400)
                : ["error" => $e->getMessage()];
        }
    }

    public function fetchTransfer(Request $request)
    {
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . env("PAYSTACK_SECRET_KEY")
            ])->accept('application/json')->get("https://api.paystack.co/transfer/$request->id_or_code");
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