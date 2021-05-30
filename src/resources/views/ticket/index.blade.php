@extends('layouts.app' )
@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <div class="container my-3">
        <div class="card shadow">
            <div class="card-header">تیکتها</div>
            <div class="card-body">

                <p><a href="{{ route('sepehrgostar.LaravelClient.ticket.create',['uid_tmp'=> Str::random(8)]) }}"
                      class="btn btn-success text-light">ایجاد تیکت</a>
                </p>

                <div class="table-responsive">

                    <table class='table text-center table-hover'>
                        <tr>
                            <th></th>
                            <th> ردیف</th>
                            <th>شماره تیکت</th>
                            <th>عنوان</th>
                            <th>وضعیت</th>
                            <th class="center">آخرین بروزرسانی</th>
                            <th></th>
                        </tr>
                        <tbody>
                        <?php $num = ((request()->page ?? 1) == 1) ? 1 : (request()->page - 1) * $rows->perpage() + 1; ?>

                        @if(isset($data))
                            @forelse($data as $row)
                                <tr @if(@$row->is_open == 0) class="table-warning" @endif>
                                    <td>


                                        @if(@$row->is_answer)
                                            <span data-toggle="tooltip" data-placement="bottom"
                                                  title="پاسخ داده شده"
                                                  class="fa fa-eye"></span>
                                        @else

                                            <span data-toggle="tooltip" data-placement="bottom"
                                                  title="پاسخ داده نشده"
                                                  class="fa fa-eye-slash"></span>
                                        @endif

                                    </td>
                                    <td>{{ @$num }}</td>
                                    <td>{{ @$row->id }}</td>
                                    <td>{{ @$row->title }}</td>
                                    <td> @if(@$row->is_open) باز @else بسته @endif</td>
                                    <td>{{ (@$row->changed_at) }}</td>
                                    <td>
                                        <div class='btn-group float-left'>
                                            <a class='btn btn-primary btn-sm text-white'
                                               href='{{route('sepehrgostar.LaravelClient.ticket.show',['ticket_id'=>$row->id,'uid_tmp'=> Str::random(8)])}}'>مشاهده
                                                تیکت</a>

                                        </div>
                                    </td>
                                </tr>
                                <?php $num = $num + 1; ?>
                            @empty
                            @endforelse
                        @endif

                        </tbody>
                    </table>

                </div>
                <div class='mt-4 float-left'>
                    {{--                {{$rows->links('vendor.pagination.bootstrap-4')}}--}}
                </div>
            </div>
        </div>
    </div>



@stop
