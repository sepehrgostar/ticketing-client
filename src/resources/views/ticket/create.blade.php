@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('vendor/sepehrgostar/bootstrap.min.css')}}">
    <script src="{{asset('vendor/sepehrgostar/alpine.js')}}" defer></script>

    <script src="{{asset('vendor/sepehrgostar/jquery.min.js')}}"></script>

    <div class="container my-3">
        <div class="card shadow">
            <div class="card-header bg-light ">ثبت تیکت</div>
            <div class="card-body">

                <form enctype="multipart/form-data" method='post'
                      action="{{ route('sepehrgostar.LaravelClient.ticket.store') }}"
                >
                    @csrf

                    <input hidden name="uid_tmp" value="{{request()->uid_tmp}}">

                    <div class="row"
                         x-data="{ contracts :{{$contracts}}, selected :'not selected',priorities :[] }"
                         x-init="$watch('selected', (id) => {  (id != -1 )  ? priorities = contracts[contracts.findIndex(contract => contract.id == id)]['priorities'] : priorities =  [] })">

                        <div class=" col-12">
                            <div class="form-group">
                                <label for="title">عنوان</label>
                                <input type="text" class="form-control" id="title_ticket" name="title"
                                       value="{{old('title')}}" required autofocus autocomplete="off">
                                <ul id="data_knowledge"></ul>
                            </div>
                        </div>

                        <div class="col-md-3 col-12">
                            <label for="‫‪managerName‬‬">خدمات</label>
                            <select x-model="selected" class="form-control" name="contract" id="contract">
                                <option x-bind:value="-1">سایر موارد</option>
                                <template x-for="item in contracts" :key="item['id']">
                                    <option x-bind:value="item['id']" x-text="item['name']"></option>
                                </template>
                            </select>
                        </div>

                        <template x-if="priorities.length > 0">
                            <div class="col-md-3 col-12">
                                <label for="priority">درجه اهمیت</label>
                                <select class="form-control" name="priority" id="priority">
                                    <template x-for="row in priorities" :key="row['id']">
                                        <option :value="row['id']" x-text="row['name']"></option>
                                    </template>
                                </select>
                            </div>
                        </template>

                        <template x-if="priorities.length <= 0">
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="priority">درجه اهمیت</label>
                                    <select class="form-control" name="priority" id="priority">
                                        @foreach ($priorities as $item )
                                            <option value="{{ $item ->id }}"
                                                    @if(old('priority') ==  $item ->id) selected @endif >{{ $item ->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </template>

                        <div class="col-md-3 col-12">
                            <div class="form-group">
                                <label for="team">نام گروه</label>
                                <select class="form-control" name="team_uid" id="team">
                                    @foreach ($teams as  $item )
                                        <option value="{{ $item->uid }}"
                                                @if(old('team_uid') ==  $item ->uid) selected @endif >{{ $item ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <p>متن تیکت</p>
                    <div class="mt-1">

                        <p class="mt-3">
                            <b> {{$signature->start_note ?? ''}}</b>
                        </p>

                        <textarea required class="form-control" rows="4" id="content"
                                  name="content">{{old("content")}}</textarea>

                        <p class="mt-3">
                            <b> {{$signature->end_note ?? ''}}</b>
                        </p>

                        @component('LaravelClient::partials.uploader', [
                        'title' => trans('ticketing::admin.max_attach_files'),
                         'params' => [
                         'user'=> json_encode(auth()->user()),
                         'api_token'=> auth()->user()->sepehrgostar_api_token,
                         'api_key' => config('LaravelClient.api_key'),
                         'uid_tmp' => request()->uid_tmp,
                         'attachable_type' => 'webine\ticketing\entities\ticketChildModel',
                         'directory' => 'ticketing/'.date('Ym'),
                         'is_private' => 1,
                        'disk' => 'local',
                        ],
                        'maxFile' => 5,
                        'acceptedFiles' => '.pdf,.jpg,.png,.jpeg,.zip,.rar,.pdf,.docx,.txt,.ppt,.xlsx,.xls,.pptx,.ppt,.doc',
                          ])
                        @endcomponent
                    </div>


                    <button type="submit" name="send"
                            class="btn btn-success">ارسال
                    </button>
                </form>

            </div>
        </div>
    </div>

@stop
