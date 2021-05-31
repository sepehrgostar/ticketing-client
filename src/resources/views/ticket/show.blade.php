<link rel="stylesheet" href="{{asset('vendor/sepehrgostar/bootstrap.min.css')}}">
<script src="{{asset('vendor/sepehrgostar/alpine.js')}}" defer></script>
<script src="{{asset('vendor/sepehrgostar/jquery.min.js')}}"></script>

<script src="{{asset('vendor/sepehrgostar/bootstrap.min.js')}}"></script>


<div class="container my-3">
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif
    @if(Session::has('message'))
        <p class="alert alert-info">{{ Session::get('message') }}</p>
    @endif

    <form id="reply" role="form" method="POST"
          action="{{route('Sepehrgostar.TicketingClient.reply',$data->ticket->id)}}"
    >
        @csrf

        <div class=" card shadow">
            <div class="card-header @if($data->ticket->is_open  == 0) bg-warning @endif">
                تیکت آیدی{{$data->ticket->id}}</div>
            <div class="card-body">

                <div class="rounded p-3 mb-4 bg-secondary text-white text-center">
                    <dvi class="row">

                        <div class="col-md-6 col-12">
                            <label><b>عنوان: {{$data->ticket->title }}</b></label>
                        </div>

                        <div class="col-md-6 col-12">
                            <label> درجه اهمیت: {{$data->ticket->priority_name }}</label>
                        </div>
                    </dvi>
                </div>

                @if(!empty($data->meta[0]))
                    <div class="row">

                        @forelse($data->meta as $meta)
                            <div class="col-md-3">
                                <div class="form-group ">
                                    <label> {{$meta->meta->name}}</label>
                                    <input class="form-group" readonly value="{{$meta->meta_value}}">
                                </div>
                            </div>

                        @empty
                        @endforelse

                    </div>
                @endif


                @if(!empty($data->childs))

                    @include('TicketingClient::ticket.child')
                @endif

                <input type="hidden" name="uid_tmp" value="{{request()->uid_tmp}}">
                <div class="col-12">
                    @if(!empty(request()->reply))

                        <label>در پاسخ به :</label>
                        <div class="d-flex justify-content-between my-1">

                            <a style="width: 95%" href="#child-{{request()->reply}}">
                                <input readonly class="form-control"
                                       value="{{strip_tags(Str::limit($data->childs->where('id',request()->reply)->first()->model->content,50)) }}">
                            </a>
                            <a class="btn btn-danger" href="{{request()->url}}"><i class="fa fa-close"
                                                                                   aria-hidden="true"> </i></a>

                        </div>
                        <input type="text" hidden name="parent_id" value="{{request()->reply}}">

                    @endif
                    <p>متن تیکت</p>
                    <div class="mt-1">

                        <textarea class="form-control" rows="4" id="content"
                                  name="content">{{old("content")}}</textarea>

                        <p class="mt-3">
                            <b> {{$signature->end_note ?? ''}}</b>
                        </p>
                        <?php
                        request()->request->add(['attachable_type' => 'webine\ticketing\entities\ticketChildModel']);
                        $uploadedFiles = app(\Sepehrgostar\TicketingClient\Http\Controllers\ticketController::class)->uploadedFiles(request());
                        ?>
                        @component('TicketingClient::partials.uploader', [
                       'title' => trans('ticketing::admin.max_attach_files'),
                       'params' => [
                           'user'=> json_encode(auth()->user()),
                           'api_token'=> auth()->user()->sepehrgostar_api_token,
                           'api_key' => config('TicketingClient.api_key'),
                           'uid_tmp' => request()->uid_tmp,
                       ],
                       'maxFile' => 5,
                       'acceptedFiles' => '.pdf,.jpg,.png,.jpeg,.zip,.rar,.pdf,.docx,.txt,.ppt,.xlsx,.xls,.pptx,.ppt,.doc',
                       'uploadedFiles' =>$uploadedFiles->files
                       ])
                        @endcomponent

                    </div>
                </div>
            </div>

            <hr>
            <div class="row">


                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="submit" name="reply"
                            class="btn btn-success ">
                        ارسال
                    </button>
                </div>


                <div class="btn-group float-left" role="group" aria-label="Basic example">
                    <button type="button"
                            class="btn btn-dark" data-bs-toggle="modal"
                            data-bs-target="#createSensitiveModal">
                        دیتا محرمانه
                    </button>

                    <a class='btn btn-secondary'
                       href='{{route('Sepehrgostar.TicketingClient.index')}}'>
                        بازگشت
                    </a>

                </div>

            </div>
        </div>

    </form>
    @include('TicketingClient::ticket.send_sensitive_data')

</div>

