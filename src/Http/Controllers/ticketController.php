<?php

namespace Sepehrgostar\TicketingClient\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use \Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ticketController extends Controller
{

    public function index()
    {

        //register user in yourDomain.sepehrgostar.com
        $this->checkUser();

        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/index', [
            'api_key' => config('TicketingClient.api_key'),
            'user' => json_encode(auth()->user()),
            'api_token' => auth()->user()->sepehrgostar_api_token
        ]);

        $data = (json_decode($response->getBody()->getContents(), false));
        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }
        return view('TicketingClient::ticket.index', compact('data'));
    }

    public function create()
    {
        //register user in yourDomain.sepehrgostar.com
        $this->checkUser();

        $base_url = config('TicketingClient.base_url');

        $response = Http::get($base_url . '/api/v1/create', [
            'api_key' => config('TicketingClient.api_key'),
            'user' => json_encode(auth()->user()),
            'api_token' => auth()->user()->sepehrgostar_api_token

        ]);

        $data = (json_decode($response->getBody()->getContents(), false));

        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }
        return view('TicketingClient::ticket.create', ['priorities' => $data->priorities, 'teams' => $data->teams, 'contracts' => json_encode($data->contracts)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => 'required',
            "priority" => 'required',
            "team_uid" => 'required',
            "content" => 'required',
        ], [
            "title.required" => 'عنوان الزامی می باشد.',
            "priority.required" => 'درجه اهمیت الزامی می باشد.',
            "team_uid.required" => 'تیم الزامی می باشد.',
            "content.required" => 'متن تیکت الزامی می باشد.',
        ]);

        $this->checkUser();
        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/store', [
            'api_key' => config('TicketingClient.api_key'),
            'api_token' => auth()->user()->sepehrgostar_api_token,
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


        return redirect()->route('sepehrgostar.ticketing.ticket.show', ['ticket_id' => $data, 'uid_tmp' => Str::random(8)]);
    }

    public function show(Request $request, $id)
    {
        $this->checkUser();

        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/show/' . $id, [
            'api_key' => config('TicketingClient.api_key'),
            'user' => json_encode(auth()->user()),
            'api_token' => auth()->user()->sepehrgostar_api_token,
            'id' => $id,
            'request' => $request
        ]);

        $data = (json_decode($response->getBody()->getContents(), false));
        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }
        return view('TicketingClient::ticket.show', ['data' => $data]);
    }

    public function reply(Request $request, $id)
    {

        $request->validate([
            "content" => 'required',
        ], [
            "content.required" => 'متن تیکت الزامی می باشد.',
        ]);

        $this->checkUser();

        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/reply/' . $id, [
            'api_key' => config('TicketingClient.api_key'),
            'api_token' => auth()->user()->sepehrgostar_api_token,
            "uid_tmp" => $request->uid_tmp,
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

    public function storeSensitive(Request $request)
    {
        $this->checkUser();

        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/store/sensitive', [
            'api_key' => config('TicketingClient.api_key'),
            'ticket_id' => $request->ticket_id,
            'content' => $request->content,
            'api_token' => auth()->user()->sepehrgostar_api_token,
            'user' => json_encode(auth()->user()),
        ]);

        $data = (json_decode($response->getBody()->getContents(), false));

        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->route('home')->with('error', $data->message);
        }

        return redirect()->back()->with($data->type, $data->content);

    }

    public function downloadAttach(Request $request)
    {
        $this->checkUser();

        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/download/attach/' . $request->uuid, [
            'api_key' => config('TicketingClient.api_key'),
            'api_token' => auth()->user()->sepehrgostar_api_token,
            'user' => json_encode(auth()->user()),
        ]);

        $file = (new Response($response, 200))
            ->header('Content-Type', $request->mime)
            ->header('Content-disposition', 'attachment; filename="' . $request->filename . '"');  //for download
        return $file;
    }

    public function uploadedFiles(Request $request)
    {
        $this->checkUser();
        $base_url = config('TicketingClient.base_url');
        $response = Http::get($base_url . '/api/v1/uploaded/file', [
            'api_key' => config('TicketingClient.api_key'),
            'api_token' => auth()->user()->sepehrgostar_api_token,
            'user' => json_encode(auth()->user()),
            'attachable_type' => $request->attachable_type,
            'uid_tmp' => $request->uid_tmp,
        ]);

        return (json_decode($response->getBody()->getContents(), false));
    }

    public function deleteAttach($id)
    {
        $base_url = config('TicketingClient.base_url');
        Http::delete($base_url . '/api/v1/attachments/delete/' . $id, [
            'api_key' => config('TicketingClient.api_key'),
            'api_token' => auth()->user()->attachable_type,
            'user' => json_encode(auth()->user()),
        ]);
    }

    public function checkUser()
    {


        if (auth()->user()->sepehrgostar_api_token == null) {
            $base_url = config('TicketingClient.base_url');
            $response = Http::get($base_url . '/api/v1/register/user', [
                'api_key' => config('TicketingClient.api_key'),
                'user' => json_encode(auth()->user()),
            ]);

            $data = (json_decode($response->getBody()->getContents(), false));

            User::find(auth()->id())->update([
                'sepehrgostar_api_token' => $data->api_token
            ]);
        }

    }

}
