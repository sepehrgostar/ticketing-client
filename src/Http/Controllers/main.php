<?php

namespace Sepehrgostar\LaravelClient\Http\Controllers;

use App\Http\Controllers\Controller;
use function Couchbase\defaultDecoder;

class main extends Controller
{

    public function index()
    {
        return view('LaravelClient::index');
    }

    public function query()
    {
defaultDecoder(ff);
    }
}
