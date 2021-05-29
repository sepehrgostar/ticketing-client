<?php

namespace Sepehrgostar\LaravelClient\Http\Controllers;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ticketController extends Controller
{

    public function index()
    {
        //register user in yourDomain.sepehrgostar.com
        $this->checkUser();

        $base_url = config('ticketapiclient.base_url');
        $response = Http::get($base_url . '/api/v1/index', [
            'api_key' => config('ticketapiclient.api_key'),
            'user' => json_encode(auth()->user()),
            'api_token' => auth()->user()->api_token
        ]);

        $data = (json_decode($response->getBody()->getContents(), false));
        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }
        return view('LaravelClient::ticket.index', compact('data'));
    }

    public function create()
    {
        //register user in yourDomain.sepehrgostar.com
        $this->checkUser();

        $base_url = config('ticketapiclient.base_url');

        $response = Http::get($base_url . '/api/v1/create', [
            'api_key' => config('ticketapiclient.api_key'),
            'user' => json_encode(auth()->user()),
            'api_token' => auth()->user()->api_token

        ]);

        $data = (json_decode($response->getBody()->getContents(), false));

        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }

        return view('LaravelClient::ticket.create', ['priorities' => $data->priorities, 'teams' => $data->teams, 'contracts' => json_encode($data->contracts)]);
    }

    public function store(Request $request)
    {
        $this->checkUser();
        $base_url = config('ticketapiclient.base_url');
        $response = Http::get($base_url . '/api/v1/store', [
            'api_key' => config('ticketapiclient.api_key'),
            'api_token' => auth()->user()->api_token,
            "uid_tmp" => $request->uid_tmp,
            "title" => $request->title,
            "contract" => $request->contract,
            "priority" => $request->priority,
            "team_uid" => $request->team_uid,
            "content" => $request['content'],
            'user' => json_encode(auth()->user()),
        ]);

        $data = (json_decode($response->getBody()->getContents(), false));
        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }

        return redirect()->route('ticket.show', ['ticket_id' => $data, 'uid_tmp' => Str::random(8)]);
    }


    public function show(Request $request, $id)
    {
        $this->checkUser();

        $base_url = config('ticketapiclient.base_url');
        $response = Http::get($base_url . '/api/v1/show/' . $id, [
            'api_key' => config('ticketapiclient.api_key'),
            'user' => json_encode(auth()->user()),
            'api_token' => auth()->user()->api_token,
            'id' => $id,
            'request' => $request
        ]);

        $data = (json_decode($response->getBody()->getContents(), false));
        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }
        return view('ticketapiclient::show', ['data' => $data]);
    }

    public function reply(Request $request, $id)
    {
        $this->checkUser();

        $base_url = config('ticketapiclient.base_url');
        $response = Http::get($base_url . '/api/v1/reply/' . $id, [
            'api_key' => config('ticketapiclient.api_key'),
            'api_token' => auth()->user()->api_token,
            "uid_tmp" => $request->uid_tmp,
            'rate' => $request->rate,
            'id' => $id,
            'parent_id' => $request->parent_id,
            "content" => $request['content'],
            'user' => json_encode(auth()->user()),
        ]);
        $data = (json_decode($response->getBody()->getContents(), false));
        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }

        return redirect()->back()->with($data->type, $data->content);
    }

    public function query()
    {

    }
}
