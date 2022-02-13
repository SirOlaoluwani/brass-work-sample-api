<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\Banks;
use Illuminate\Http\Request;

class BankController extends Controller
{
    use Banks;
}