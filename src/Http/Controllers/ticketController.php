<?php

namespace Sepehrgostar\TicketingClient\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ticketController extends Controller
{

    public function index()
    {

        //register user in yourDomain.sepehrgostar.com

        $this->checkUser();

        $response = $this->post([], '/api/v1/index');

        $data = (json_decode($response->getBody()->getContents(), false));
        if (@$data->type == "errors") {
            return redirect()->back()->withErrors($data->message);
        }
        return view('TicketingClient::ticket.index', compact('data'));
    }

    public function create()
    {
        //register user in yourDomain.sepehrgostar.com
        $this->checkUser();

        $response = $this->post([], '/api/v1/create');

        $data = (json_decode($response->getBody()->getContents(), false));

        if (@$data->type == "errors") {
            return redirect()->back()->withErrors($data->message);
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


        $response = $this->post([
            "uid_tmp" => $request->uid_tmp,
            "title" => $request->title,
            "contract" => $request->contract,
            "priority" => $request->priority,
            "team_uid" => $request->team_uid,
            "content" => $request['content'],
        ], '/api/v1/store');


        $data = (json_decode($response->getBody()->getContents(), false));
        if (@$data->type == "errors") {
            return redirect()->back()->withErrors($data->message);
        }

        return redirect()->route('Sepehrgostar.TicketingClient.show', ['ticket_id' => $data, 'uid_tmp' => Str::random(8)]);
    }

    public function show(Request $request, $id)
    {

        $response = $this->post([
            'id' => $id,
            'request' => $request,
        ], '/api/v1/show' . $id);


        $data = (json_decode($response->getBody()->getContents(), false));
        if (@$data->type == "errors") {
            return redirect()->back()->withErrors($data->message);
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

        $response = $this->post([
            "uid_tmp" => $request->uid_tmp,
            'id' => $id,
            'parent_id' => $request->parent_id,
            "content" => $request['content'],
        ], '/api/v1/reply' . $id);

        $data = (json_decode($response->getBody()->getContents(), false));

        if (isset($data->status) and ($data->status == "no_connect")) {
            return redirect()->back()->with('errors', $data->message);
        }

        return redirect()->back()->with($data->type, $data->content);
    }

    public function storeSensitive(Request $request)
    {

        $response = $this->post([
            'ticket_id' => $request->ticket_id,
            'content' => $request->content,
        ], '/api/v1/store/sensitive');

        $data = (json_decode($response->getBody()->getContents(), false));

        if (@$data->type == "errors") {
            return redirect()->back()->withErrors($data->message);
        }

        return redirect()->back()->with($data->type, $data->content);

    }


    public function downloadAttach(Request $request)
    {

        $response = $this->post([], '/api/v1/download/attach/' . $request->uuid);

        $file = (new Response($response, 200))
            ->header('Content-Type', $request->mime)
            ->header('Content-disposition', 'attachment; filename="' . $request->filename . '"');  //for download
        return $file;
    }

    public function uploadedFiles(Request $request)
    {

        $response = $this->post([
            'attachable_type' => $request->attachable_type,
            'uid_tmp' => $request->uid_tmp
        ], '/api/v1/uploaded/file');

        return (json_decode($response->getBody()->getContents(), false));
    }

    public function deleteAttach($id)
    {

        $response = $this->post([
            'attachable_type' => $request->attachable_type,
            'uid_tmp' => $request->uid_tmp
        ], '/api/v1/attachments/delete/' . $id);
    }


    public function post($data, $url)
    {
        return Http::post(config('TicketingClient.base_url') . $url, array_merge($data, [
            'api_key' => config('TicketingClient.api_key'),
            'api_token' => auth()->user()->sepehrgostar_api_token,
            'user' => [
                'name' => auth()->user()[config('TicketingClient.user.name', 'name')],
                'lname' => auth()->user()[config('TicketingClient.user.username', 'lname')],
                'mobile' => auth()->user()[config('TicketingClient.user.mobile', 'mobile')],
                'email' => auth()->user()[config('TicketingClient.user.email', 'email')],
                'username' => auth()->user()[config('TicketingClient.user.username', 'username')],
            ],
        ]));

    }

    public function checkUser()
    {

        //فقط یکبار اجرا میشود زمانی که  api_token نداشت
        if (auth()->user()->sepehrgostar_api_token == null) {

            $response = $this->post([], '/api/v1/register/user');
            $data = json_decode($response->getBody()->getContents(), false);

            User::find(auth()->id())->update([
                'sepehrgostar_api_token' => $data->api_token
            ]);

        }

    }

}
