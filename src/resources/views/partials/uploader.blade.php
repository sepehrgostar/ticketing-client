<style>
    .dropzone {
        border: 2px dashed #89a4ba;
        border-radius: 10px;
        background: white;
    }

    .dropzone .dz-message {

        margin: 0em 0;
    }
</style>

@php $dropzoneId = isset($dz_id) ? $dz_id : Str::random(8) @endphp
<div id="{{$dropzoneId}}" class="dropzone">
    <div class="dz-default dz-message">
        <h5>{{ $title  ?? ''}}</h5>
        <br>
        <p>Drag & Drop</p>
        <small> حداکثر سایر مجاز {{ config('attachment.max_size', 2000) / 1000 }} MB</small>
    </div>
</div>
<!-- Dropzone {{ $dropzoneId }} -->

<link href="{{asset('vendor/sepehrgostar/ticketingClient/dropzone.min.css')}}" rel="stylesheet">
<script src="{{asset('vendor/sepehrgostar/ticketingClient/dropzone.min.js')}}"></script>

<script>

    Dropzone.autoDiscover = false;

    $(function () {
        $("#{{ $dropzoneId }}").dropzone({
            url: "{{config('TicketingClient.base_url')}}/api/v1/store/file",
            method: "post",
            addRemoveLinks: true,
            maxFiles: {{@$maxFile ? $maxFile : 5}},
            dictRemoveFile: "حذف فایل",
            maxFilesize: {{ config('attachment.max_size', 20000) / 1000 }},
            acceptedFiles: "{!! isset($acceptedFiles) ? $acceptedFiles : config('attachment.allowed') !!}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},

            params: {!! isset($params) ? json_encode($params) : '{}'  !!},
            init: function () {
                var uploadedFiles = [];
                @if(isset($uploadedFiles) && count($uploadedFiles))

                    uploadedFiles = {!! json_encode($uploadedFiles) !!};
                var self = this;

                uploadedFiles.forEach(function (file) {
                    var uploadedFile = {
                        name: file.filename,
                        size: file.size,
                        type: file.mime,
                        uuid: file.uuid,
                        dataURL: file.link
                    };
                    console.log(file.link);
                    self.emit("addedfile", uploadedFile);

                    if (file.mime.indexOf('image') !== -1) {

                        self.emit("thumbnail", uploadedFile, getIconFromFilename(uploadedFile));

                    } else {
                        self.emit("thumbnail", uploadedFile, getIconFromFilename(uploadedFile));
                    }

                    self.emit("complete", uploadedFile);
                    self.emit("success", uploadedFile);

                    var anchorEl = document.createElement('a');
                    anchorEl.setAttribute('href', '{{config('TicketingClient.base_url')}}/api/v1/download/attach/' + uploadedFile.uuid);
                    anchorEl.setAttribute('target', '_blank');
                    anchorEl.className = 'dz-remove';
                    anchorEl.innerHTML = "دانلود فایل";
                    uploadedFile.previewTemplate.appendChild(anchorEl);

                });

                @endif

                    this.on('addedfile', function (file) {

                    var thumb = getIconFromFilename(file);
                    $(file.previewElement).find(".dz-image img").attr("src", thumb);

                    var _i, _len;
                    for (_i = 0, _len = uploadedFiles.length; _i < _len; _i++) {
                        if (uploadedFiles[_i].filename === file.name && uploadedFiles[_i].size === file.size) {
                            file.ignore_delete_in_server = true;
                            this.removeFile(file);
                        }
                    }

                });

                this.on('success', function (file, response) {
                    if (response) {
                        uploadedFiles.push(response.data);
                        var anchorEl = document.createElement('a');
                        anchorEl.setAttribute('href', response.download);
                        anchorEl.setAttribute('target', '_blank');
                        anchorEl.className = 'dz-remove';
                        anchorEl.innerHTML = "دانلود فایل";
                        file.previewTemplate.appendChild(anchorEl);
                    }
                });

                this.on("removedfile", function (file) {

                    var found = uploadedFiles.find(function (item) {
                        return (item.filename === file.name) && (item.size === file.size);
                    });

                    if (found && file.ignore_delete_in_server !== true) {

                        index = uploadedFiles.findIndex(x => x.id === found.id);
                        uploadedFiles.splice(index, 1);

                        $.ajax({
                            url: "/ticketing/delete/file/" + found.id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                console.log('deleted');
                            },
                            error: function (response) {
                                console.log(response)
                            }
                        });
                    }
                });

                this.on('error', function (file, response) {
                    var errMsg = response;

                    if (response.message) errMsg = response.message;
                    if (response.file) errMsg = response.file[0];

                    $(file.previewElement).find('.dz-error-message').text(errMsg);
                });
            }
        });
    });

    function getIconFromFilename(file) {

        var ext = file.name.split('.').pop().toLowerCase();

        if (ext === 'docx') {
            ext = 'doc'
        } else if (ext === 'xlsx') {
            ext = 'xls'
        }

        if (ext === 'jpg' || ext === 'png' || ext === 'jpeg' || ext === 'gif') {
            ext = 'picture'
        }

        return "/vendor/sepehrgostar/ticketingClient/images/icon/" + ext + ".svg";
    }
</script>
