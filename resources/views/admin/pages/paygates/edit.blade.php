@extends('admin.master')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/CustomStyle.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">PayGates</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <form role="form" action="{{ url('/admin/paygates/'.$payGate->id.'/update') }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <section class="content">
                <div class="col-7 float-left">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Information</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                    <i class="fas fa-minus"></i></button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                                    <i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Name <span class="icon-required">*</span></label>
                                <input type="text" class="form-control" id="exampleInputEmail1" name="name" value="{{ $payGate->name }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Code <span class="icon-required">*</span></label>
                                <input type="text" class="form-control" id="exampleInputPassword1" name="code" value="{{ $payGate->code }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Url <span class="icon-required">*</span></label>
                                <input type="text" class="form-control" id="exampleInputPassword1" name="url" value="{{ $payGate->url }}">
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <div class="col-5 float-right">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configs</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                    <i class="fas fa-minus"></i></button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                                    <i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php $i = 1; ?>
                            @foreach($payGate->conf as $key=>$conf)
                                <div class="form-group">
                                    <label for="exampleInputPassword1">{{ $key }} <span class="icon-required">*</span></label>
                                    <input type="text" class="form-control" id="exampleInputPassword1" name="{{ $key }}" value="{{ $conf }}">
                                </div>
                                <?php $i++; ?>
                            @endforeach
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-pen"></i> Update</button>
                            </div>
                        </div>
                        <!-- /.card-footer-->
                    </div>
                </div>

            </section>
        </form>
        <!-- /.content -->
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('') }}"></script>
@endsection
