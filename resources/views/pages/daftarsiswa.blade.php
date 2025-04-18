@extends('main')

@section('konten')
<div class="container-fluid">

<!-- Page Heading -->
<div class="row align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>


    <div class="container-fluid bg-white">
        <form action="" method="POST"  enctype="multipart/form-data">
                                <!-- <form class="user"> -->
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-12 mt-3 mb-3 mb-sm-0">
                                <input type="text"  class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Full Name" name="nama">
                        </div>
                        <div class="col-sm-6  mt-3">
                            <input type="number" class="form-control form-control-user" id="exampleLastName"
                                placeholder="Pendapatan Ayah" name="salary_ayah">
                        </div>
                            <div class="col-sm-6  mt-3">
                            <input type="number" class="form-control form-control-user" id="exampleLastName"
                                placeholder="Pendapatan ibu" name="salary_ibu">
                        </div>
                            <div class="col-sm-6  mt-3">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="inputGroupSelect01">Options</label>
                                    </div>
                                    <select class="custom-select" name="pend_ayah" id="inputGroupSelect01">
                                        <option selected>Choose...</option>
                                    
                                    </select>
                                </div>
                            </div>

                                <div class="col-sm-6  mt-3">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="inputGroupSelect01">Options</label>
                                    </div>
                                    <select class="custom-select" name="pend_ibu" id="inputGroupSelect01">
                                        <option selected>Choose...</option>
                                        
                                    </select>
                                </div>
                            </div>
                    </div>
                    <!-- <div class="form-group">
                        <input type="email" class="form-control form-control-user" id="exampleInputEmail"
                            placeholder="Email Address">
                    </div> -->
                    
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit">Daftar</button>
                    </div>
                    
        </form>
    </div>
</div>


</div>
@endsection