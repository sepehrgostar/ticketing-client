@foreach($data->childs as $row)
    <div class="card px-3" id="child-{{$row->id}}">
        <div class="d-flex justify-content-between my-1">
            <div class="p-1">
                <small class="badge @if(@$row->user->is_admin) badge-primary @else badge-secondary  @endif ">

                    @if(auth()->user()->is_admin ==1 )
                        <span class="tahoma"> {{@$row->user->username }}</span>
                    @elseif(auth()->user()->is_admin ==0
{{-- and setting('ticketing_show_operator_username') == 1--}}
 )
                        <span class="tahoma"> {{auth()->user()['username'] ?? '---'}}</span>
                    @elseif(auth()->user()->id !=$row->user->id)
                        @lang('ticketing::profile.operator')
                    @endif

                </small>
                <small>{{'ایجاد شده :'}} {{ Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</small>

            </div>

            <div class="mt-2">
                @if($row->user->id==auth()->id())

                    <span class='show-modal-delete-form mx-1'
                          onclick="sendIDtoDeleteFromChild({{ $data->ticket->id  }},{{ $row->id }})">
                            <i class="fas fa-trash-alt"></i>
                        </span>

                @endif

                <span class="copy-clipboard mx-1" data-clipboard-text="{{url()->full().'#'.'child-'.$row->id}}">
                    <i class="fas fa-copy"></i></span>

            </div>

        </div>

        <div class="row">

            <div class="col ">

                <hr class="m-0">

                <style>

                    .br-theme-bars-1to10 .br-widget .br-current-rating {
                        font-size: 15px;
                    }

                    .br-theme-bars-1to10 .br-widget a {
                        display: block;
                        width: 5px;
                        padding: 5px 0;
                        height: 5px;
                        float: left;
                        background-color: #fbedd9;
                        margin: 1px;
                        text-align: center;
                    }

                    .br-theme-bars-1to10 .br-widget {
                        height: 0px;
                    }

                    .br-theme-bars-1to10 .br-widget .br-current-rating {
                        font-size: 15px;
                        line-height: 1;
                        float: left;
                        padding: 0 5px 0 2px;
                        color: #edb867;
                        font-weight: 400;
                    }
                </style>

                <div class=" mb-3">
                    @if(!empty($row->parent_id))
                        <a href="#child-{{$row->parent_id}}">
                            <input readonly class="form-control" maxlength="10"
                                   value=" {{Str::limit($row->reply->model->content,50) }}">
                        </a>
                    @endif
                    {!! $row->content !!}
                </div>

                <div class="d-flex justify-content-between ">
                    <small class="rtl">
                        {{($row->created_at)}}
                    </small>
                </div>
                <div class="col-12">
                    @if(!empty($row->attachments))
                        <ul id="attached-files-list" class="p-0">

                            @foreach($row->attachments as $attach)
                                <li>
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                    <span class="filename ellipsis"></span>
                                    <span class="  tahoma text-muted text-left float-left ">{{round(($attach->size / 1024) / 1024,2) }}MB</span>
                                    <a class="tahoma text-secondary  mr-2" target="_blank"
                                       href="{{route('sepehrgostar.LaravelClient.ticket.download.attach',['uuid'=>$attach->uuid,'filename'=>$attach->filename,'mime'=>$attach->mime])}}"> {{$attach->filename}} </a>
                                </li>
                            @endforeach

                        </ul>
                    @endif
                </div>
                <div class="col-12">
                    @if(!empty($row->model->auto_complete))
                        <ul id="attached-files-list" class="p-0">

                            @foreach($row->model->auto_complete->attachments as $attach)
                                <li>
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                    <span class="filename ellipsis"></span>
                                    <span class="  tahoma text-muted text-left float-left ">{{round(($attach->size / 1024) / 1024,2) }}MB</span>
                                    <a class="tahoma text-secondary  mr-2" target="_blank"
                                       href="{{route('sepehrgostar.LaravelClient.ticket.download.attach',['uuid'=>$attach->uuid])}}"> {{$attach->filename}} </a>
                                </li>
                            @endforeach

                        </ul>

                        <div class="table-responsive">
                            @forelse($row->attachments_limit  as $attach_limit)
                                <table class="table table-hover col-6">
                                    <tr>
                                        <td>  {{trans('ticketing::admin.link_file')}} </td>
                                        <td><i class="fa fa-download" aria-hidden="true"></i>
                                            <span class="filename ellipsis"></span>
                                            <a class="tahoma text-primary  mr-2" target="_blank"
                                               href="{{route('ticketing.download.attach.autoresponse',['uid'=>$attach_limit->uid])}}"> {{$attach_limit->attachment->filename}} </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{trans('ticketing::admin.size_file')}}
                                        </td>
                                        <td>
                                            <span class="filename ellipsis"></span>
                                            <span class="  tahoma text-muted text-right float-right ">{{round(($attach_limit->attachment->size / 1024) / 1024,2) }}MB</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            {{trans('ticketing::admin.number_download_allow')}}
                                        </td>
                                        <td>
                                            {{($attach_limit->max_download-$attach_limit->count_download)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{trans('ticketing::admin.linkـexpiration')}}
                                        </td>
                                        <td>
                                            {{jdate($attach_limit->expire_at)}}
                                        </td>
                                    </tr>
                                </table>
                            @empty
                            @endforelse
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>


@endforeach
