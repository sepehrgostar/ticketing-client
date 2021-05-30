<div class="modal fade" id="createSensitiveModal" tabindex="-2" role="dialog" aria-labelledby="createModalTitle"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{route('Sepehrgostar.TicketingClient.sensitive')}}">
                @csrf
                <input hidden name="ticket_id" value="{{$data->ticket->id}}">
                <div class="modal-body">
                    <p class="text-center font-weight-bold text-danger">دیتای محرمانه</p>
                    <hr>
                    <textarea class="form-control ltr tahoma" rows="4" name="content">@php
                            $base_url = config('TicketingClient.base_url');
                            $response = Http::get($base_url . '/api/v1/create/sensitive', [
                                'api_key' => config('TicketingClient.api_key'),
                                'ticket_id' => $data->ticket->id,
                                 'api_token' => auth()->user()->sepehrgostar_api_token,
                                'user' => json_encode(auth()->user()),
                            ]);

                            $sensitive = (json_decode($response->getBody()->getContents(), false));
                            echo @$sensitive->content;
                        @endphp</textarea>

                    <button type="submit" name="send" class="btn btn-success mt-3"><i
                            class="fa fa-check" aria-hidden="true"></i>
                        ثبت
                    </button>

                </div>
            </form>

        </div>
    </div>
</div>
