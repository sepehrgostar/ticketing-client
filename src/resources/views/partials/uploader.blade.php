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


<script>

    // Turn off auto discovery
    Dropzone.autoDiscover = false;

    $(function () {
        // Attach dropzone on element
        $("#{{ $dropzoneId }}").dropzone({
            url: "{{config('LaravelClient.base_url')}}/api/v1/attachments/store",
            method: "post",
            addRemoveLinks: true,
            maxFiles: {{@$maxFile ? $maxFile : 5}},
            dictRemoveFile: "حذف فایل",
            maxFilesize: {{ config('attachment.max_size', 20000) / 1000 }},
            acceptedFiles: "{!! isset($acceptedFiles) ? $acceptedFiles : config('attachment.allowed') !!}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},

            params: {!! isset($params) ? json_encode($params) : '{}'  !!},
            init: function () {
                // uploaded files
                var uploadedFiles = [];
                @if(isset($uploadedFiles) && count($uploadedFiles))

                // show already uploaded files
                uploadedFiles = {!! json_encode($uploadedFiles) !!};
                var self = this;

                uploadedFiles.forEach(function (file) {
                    // Create a mock uploaded file:
                    var uploadedFile = {
                        name: file.filename,
                        size: file.size,
                        type: file.mime,
                        uuid: file.uuid,
                        dataURL: file.link //فقط برای private === 0
                    };
                    console.log(file.link);
                    // Call the default addedfile event
                    self.emit("addedfile", uploadedFile);

                    // Image? lets make thumbnail
                    if (file.mime.indexOf('image') !== -1) {

                        @if($params['is_private'] === 1 )

                        //  در اینجا میتوانیم به جای تابع createThumbnailFromUrl از تابع createThumbnail استفاده کنیم و بجای اینکه از link تصویر thumb را بسازیم از data فایل thumb قابل ساختن است
                        //با تابع createThumbnail

                        self.emit("thumbnail", uploadedFile, getIconFromFilename(uploadedFile));

                        @else

                        //ساخت thumb ار روی link
                        self.createThumbnailFromUrl(
                            uploadedFile,
                            self.options.thumbnailWidth,
                            self.options.thumbnailHeight,
                            self.options.thumbnailMethod,
                            true, function (thumbnail) {
                                self.emit('thumbnail', uploadedFile, thumbnail);
                            });

                        @endif

                    } else {
                        // we can get the icon for file type
                        self.emit("thumbnail", uploadedFile, getIconFromFilename(uploadedFile));
                    }

                    // fire complete event to get rid of progress bar etc
                    self.emit("complete", uploadedFile);
                    self.emit("success", uploadedFile);

                    // Download link
                    var anchorEl = document.createElement('a');
                    anchorEl.setAttribute('href', '/download/attach/' + uploadedFile.uuid);
                    anchorEl.setAttribute('target', '_blank');
                    anchorEl.className = 'dz-remove';
                    anchorEl.innerHTML = "دانلود فایل";
                    uploadedFile.previewTemplate.appendChild(anchorEl);

                });

                @endif

                // Handle added file
                this.on('addedfile', function (file) {

                    var thumb = getIconFromFilename(file);
                    $(file.previewElement).find(".dz-image img").attr("src", thumb);

                    var _i, _len;
                    for (_i = 0, _len = uploadedFiles.length; _i < _len; _i++) {
                        if (uploadedFiles[_i].filename === file.name && uploadedFiles[_i].size === file.size) {
                            file.ignore_delete_in_server = true; //دستی ایجاد کرده ام چون بعد از تکرار شدن فایل اصلی را حذف میکند
                            this.removeFile(file);
                        }
                    }

                });

                this.on('success', function (file, response) {
                    if (response) {
                        uploadedFiles.push(response.data);
                        // Download link
                        var anchorEl = document.createElement('a');
                        anchorEl.setAttribute('href', response.download);
                        anchorEl.setAttribute('target', '_blank');
                        anchorEl.className = 'dz-remove';
                        anchorEl.innerHTML = "دانلود فایل";
                        file.previewTemplate.appendChild(anchorEl);
                    }
                });

                // handle remove file to delete on server
                this.on("removedfile", function (file) {

                    // try to find in uploadedFiles
                    var found = uploadedFiles.find(function (item) {
                        // check if filename and size matched
                        return (item.filename === file.name) && (item.size === file.size);
                    });

                    // If got the file lets make a delete request by id
                    if (found && file.ignore_delete_in_server !== true) {

                        index = uploadedFiles.findIndex(x => x.id === found.id);
                        uploadedFiles.splice(index, 1);

                        $.ajax({
                            url: "/sepehrgostar/laravelClient/ticket/delete/file/" + found.id,
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

                // Handle errors
                this.on('error', function (file, response) {
                    var errMsg = response;

                    if (response.message) errMsg = response.message;
                    if (response.file) errMsg = response.file[0];

                    $(file.previewElement).find('.dz-error-message').text(errMsg);
                });
            }
        });
    });

    // Get Icon for file type
    function getIconFromFilename(file) {

        // get the extension
        var ext = file.name.split('.').pop().toLowerCase();

        // handle the alias for extensions
        if (ext === 'docx') {
            ext = 'doc'
        } else if (ext === 'xlsx') {
            ext = 'xls'
        }

        if (ext === 'jpg' || ext === 'png' || ext === 'jpeg' || ext === 'gif') {
            ext = 'picture'
        }

        return "/images/icon/" + ext + ".svg";

    }
</script>
