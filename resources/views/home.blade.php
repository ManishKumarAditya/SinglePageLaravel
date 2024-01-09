@extends('layouts.app')
@section('content')
<div class="row mt-5">
    <div class="col-lg-3">
        <ul class="list-group">
            <li class="list-group-item active" aria-current="true">Fill the form Details</li>
        </ul>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4>Fill the details</h4>
            </div>
            <div class="card-body">
                <div class="text-center" id="uploaded_image"></div>

                <form id="myForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name"
                            placeholder="Enter Your Name.." required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                    </div>
                    <div class="mb-3">
                        <label for="Image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept=".jpg" id="image" required>
                    </div>

                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="" disabled selected>Select option</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        @error('gender')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" id="address"
                            placeholder="Enter Your Address.." required>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <input type="submit" name="submit" id="saveForm" class="btn btn-primary" value="Submit">
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row mt-5 d-flex justify-content-center">
    <div class="col-lg-12">
        <button class="btn btn-secondary mb-2" id="sortById">Sort by ID</button>
        <button class="btn btn-secondary mb-2" id="sortByName">Sort by Name</button>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="render">

            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById("saveForm").addEventListener("click", (e) => {
        e.preventDefault();

        const name = document.querySelector("#name").value;
        const gender = document.querySelector("#gender").value;
        const address = document.querySelector("#address").value;
        const image = document.getElementsByName('image')[0];

        // Check if required fields are empty
        if (!name.trim() || !gender.trim() || !address.trim()) {
            // alert('Please fill the all required fields.');
            document.getElementById('uploaded_image').innerHTML = '<div class="alert alert-danger">Please fill the all required fields</div>';

            return;
        }

        const file = image.files[0];

        if(!['image/jpeg', 'image/png'].includes(file.type))
        {
            document.getElementById('uploaded_image').innerHTML = '<div class="alert alert-danger">Only .jpg and .png image are allowed</div>';

            document.getElementsByName('image')[0].value = '';
            
            return;
        }

        // check file size (< 2MB)
        if(file.size > 2 * 1024 * 1024)
        {
            document.getElementById('uploaded_image').innerHTML = '<div class="alert alert-danger">File must be less than 2 MB</div>';

            document.getElementsByName('image')[0].value = '';
            return;
        }

        const formData = new FormData(document.getElementById('myForm'));

        // append image in formData
        formData.append('image', file);

        // loop apply to each element of fromData
        formData.forEach((value, key) => {
            formData[key] = key === 'image' ? value.name : value;
        });

        fetch('json-data-save', {
            method: 'POST',
            body: formData
        })

        .then(data => {
            console.log('Data stored successfully:', data);
            document.querySelector("#name").value = '';
            document.querySelector("#gender").value = '';
            document.querySelector("#address").value = '';
            document.getElementsByName('image')[0].value = '';
            document.getElementById('uploaded_image').innerHTML = '';
    
            getAll();
        })
        .catch(error => {
            console.error('Error storing data:', error);
        });
    });

    
    let records = ""
        const render = document.querySelector("#render");
        getAll = async () => {
            try {
                const data = await fetch("fetch-data")
                const response = await data.json()

                const sortedDataById = response.slice().sort((a, b) => a.id - b.id);
                const sortedDataByName = response.slice().sort((a, b) => a.name.localeCompare(b.name));

                const displayData = (data) => {
                    records = data.map((single, index) => {
                        return `<tr>
                            <td>${index + 1}</td>  
                            <td>${single.name}</td>
                            <td><img style="border-radius:50%;object-fit:cover" height="90px" width="90px" alt="image" src=${single.image}>
                                <a href="${single.image}" class="btn font-weight-bold btn-sm btn-success" download>Download</a>
                            </td>
                            <td>${single.gender}</td>
                            <td>${single.address}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                <a href="#" class="btn btn-sm btn-warning">View</a></td>
                            </td>
                        </tr>`;
                    });

                    render.innerHTML = records.join('');
                };

                // Display sorted data initially (e.g., by ID)
                displayData(sortedDataById);

                // you can trigger sorting by other columns
                document.getElementById("sortById").addEventListener("click", () => displayData(sortedDataById));
                document.getElementById("sortByName").addEventListener("click", () => displayData(sortedDataByName));

                setTimeout(() => {
                    records = response.map((single, index) => {
                    return (`<tr>
                    <td>${index + 1}</td>  
                    <td>${single.name}</td>
                    <td><img style="border-radius:50%;object-fit:cover" height="90px" width="90px" alt="image" src="${single.image}">
                        <a href="${single.image}" class="btn font-weight-bold btn-sm btn-success" download>Download</a>
                    </td>
                    <td>${single.gender}</td>
                    <td>${single.address}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                        <a href="#" class="btn btn-sm btn-warning">View</a></td>
                    </tr> `)
                    })
                    
                    // render.innerHTML = records.join(''); 
                    console.log(response);
                }, 1000)

            } catch (error) {
                console.log(error);
            }
        }
        getAll();
</script>
@endsection

