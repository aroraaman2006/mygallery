@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>Images</div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group">
                                <a href="javascript:filter_data('')" class="list-group-item list-group-item-action">All</a>
                                <a href="javascript:filter_data(1)" class="list-group-item list-group-item-action">Category 1</a>
                                <a href="javascript:filter_data(2)" class="list-group-item list-group-item-action">Category 2</a>
                                <a href="javascript:filter_data(3)" class="list-group-item list-group-item-action">Category 3</a>
                              </div>
                        </div>

                        <div class="col-md-9">

                            <div class="row">
                                <div class="col-md-12">
                                    
                                    @if ($errors->any())
                                        @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger">
                                            <strong>{{$error}}</strong>     
                                        </div>    
                                        @endforeach                                        
                                    @endif

                                    <button data-toggle="collapse" data-target="#image-form-div" class="btn btn-success">Upload Images</button>

                                    <div class="collapse" id="image-form-div">
                                        <br>
                                        
                                        <form action="{{route('upload-image')}}" method="post" id="image-form" enctype="multipart/form-data">
                                            
                                            @csrf
                                            <div class="form-group">
                                              <label for="caption">Caption</label>
                                              <input type="text" class="form-control" id="caption" name="caption">
                                            </div>
        
                                            <div class="form-group">
                                                <label for="category">Select Category:</label>
                                                <select class="form-control" id="category" name="category">
                                                    <option value="" disabled selected hidden>Select a category</option>
                                                    <option value="1">Category 1</option>
                                                    <option value="2">Category 2</option>
                                                    <option value="3">Category 3</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="control-label">Upload Image</label>
                                                <div class="preview-zone hidden">
                                                  <div class="box box-solid">
                                                    <div class="box-header with-border">
                                                      <div><b>Preview</b></div>
                                                      <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-danger btn-xs remove-preview">
                                                          <i class="fa fa-times"></i> Cancel
                                                        </button>
                                                      </div>
                                                    </div>
                                                    <div class="box-body"></div>
                                                  </div>
                                                </div>
                                                <div class="dropzone-wrapper">
                                                  <div class="dropzone-desc">
                                                    <i class="glyphicon glyphicon-download-alt"></i>
                                                    <p>Choose an image file or drag it here.</p>
                                                  </div>
                                                  <input type="file" name="image" class="dropzone">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                          </form>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="row">
                                        @if ($images->count()>0)
                                            @foreach ($images as $image)
                                                <div class="col-md-3 mb-4">
                                                    <a href="#">
                                                        <img src="{{ asset('uploaded_images/thumbnails/'.$image->image) }}" height="100%" width="100%">
                                                    </a>
                                                    <br>
                                                    <div class="text-center mt-1">
                                                        <button class="btn btn-success" href="{{ asset('uploaded_images/'.$image->image) }}" class="fancybox" data-caption="{{$image->caption}}" data-fancybox="all" data-id={{$image->id}}>View</button>
                                                        <a href={{"delete/".$image['id']}} class="btn btn-danger" onClick="return confirm('Are you sure you want to delete this?');" >Delete</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <br><br><br>
                                    {{$images->appends(Request::query())->links()}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>

        var query = {};

        function filter_data(value){
            Object.assign(query, {'category':value});
            window.location.href="{{route('home')}}"+'?'+$.param(query);
        }

        $("#image-form").validate({
        rules: {
            caption: {
            required: true,
            maxlength: 255
            },

            category: {
                required: true
            },

            image: {
                required: true,
                extension: "png|jpeg|jpg|bmp"
            }
        },
        messages: {
            caption: {
                required: "Please add a valid caption",
                maxlength: "Caption must not exceed 255 characters!"
            },

            category: {
                required: "Please select a category!"
            },

            image:{
                required: "Please select an image to upload!",
                extension: "Upload PNG/JPEG/JPG/BMP files only!"
            }
        }
        });

        function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
            var htmlPreview =
                '<img width="400" height="400" src="' + e.target.result + '" />' +
                '<p>' + input.files[0].name + '</p>';
            var wrapperZone = $(input).parent();
            var previewZone = $(input).parent().parent().find('.preview-zone');
            var boxZone = $(input).parent().parent().find('.preview-zone').find('.box').find('.box-body');

            wrapperZone.removeClass('dragover');
            previewZone.removeClass('hidden');
            boxZone.empty();
            boxZone.append(htmlPreview);
            };

            reader.readAsDataURL(input.files[0]);
        }
        }

        function reset(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
        }

        $(".dropzone").change(function() {
        readFile(this);
        });

        $('.dropzone-wrapper').on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
        });

        $('.dropzone-wrapper').on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        });

        $('.remove-preview').on('click', function() {
        var boxZone = $(this).parents('.preview-zone').find('.box-body');
        var previewZone = $(this).parents('.preview-zone');
        var dropzone = $(this).parents('.form-group').find('.dropzone');
        boxZone.empty();
        previewZone.addClass('hidden');
        reset(dropzone);
        });

    </script>
@endsection
