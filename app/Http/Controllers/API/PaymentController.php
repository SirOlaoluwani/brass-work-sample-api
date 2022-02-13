<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkTransfer;
use App\Traits\PaymentTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use PaymentTrait;

    public function makeTransfer(Request $request)
    {
        $transferRecipientDetails = $this->createTransferReceipient($request);
        if ($transferRecipientDetails) {

            $transferRequest = new \Illuminate\Http\Request();
            $transferRequest->replace([
                "amount" => $request->amount,
                "recipient" => $transferRecipientDetails["data"]["recipient_code"],
                "reason" => $request->description
            ]);

            return $this->initiateTransfer($transferRequest);
        }
    }
}