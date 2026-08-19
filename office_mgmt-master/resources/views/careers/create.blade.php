<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Internship Interest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-12 text-center mb-5">
                            <img src="{{ asset('logo.png') }}" alt="">
                        </div>
                        <div class="col-sm-6">
                            <h1>Career</h1>
                        </div>

                        <div class="col-sm-6 text-end">
                            <a href="#" onclick="history.back(); return false;" class="btn btn-primary">Back To
                                Home</a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('career.store') }}" class="form-inline"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">Basic Details</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="row">
                                        @php
                                            $career = session('career');
                                        @endphp
                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>Name</label>
                                            <input type="text" name="name"
                                                value="{{ old('name') }}"
                                                class="form-control" required placeholder="Name">
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>Email</label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                class="form-control" required placeholder="Email">
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>Mobile</label>
                                            <input type="text" name="mobile" value="{{ old('mobile') }}"
                                                class="form-control" required placeholder="Mobile">
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>City</label>
                                            <input type="text" name="city" value="{{ old('city') }}"
                                                class="form-control" required placeholder="City">
                                        </div>
                                        <div class="form-group mr-2 mb-2 col-md-8">
                                            <label>Address</label>
                                            <input type="text" name="address" value="{{ old('address') }}"
                                                class="form-control" required placeholder="Address">
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>Pin code</label>
                                            <input type="text" name="pincode" value="{{ old('pincode') }}"
                                                class="form-control" required placeholder="Pincode">
                                        </div>
                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>State</label>
                                            <select name="state_id" class="form-control" required>
                                                <option value="">Select State</option>
                                                @foreach (\App\Models\State::all() as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ old('state_id', request('state_id')) == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>Office Location</label>
                                            <select name="office_location" class="form-control" required>
                                                <option value="">Select Office Location</option>
                                                @php
                                                    $locations = ['Patna', 'Noida', 'Ranchi', 'Durgapur'];
                                                @endphp
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location }}"
                                                        {{ old('office_location') == $location ? 'selected' : '' }}>
                                                        {{ $location }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">Department & Skills</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="form-group mr-2 mb-2 col-md-4">
                                            <label>Department</label>
                                            <select name="department_skills_id" id="department_skills_id"
                                                class="form-control" required>
                                                <option value="">Select Department</option>
                                                @foreach (\App\Models\DepartSkill::all() as $department)
                                                    <option value="{{ $department->id }}"
                                                        {{ old('department_skills_id', request('department_skills_id')) == $department->id ? 'selected' : '' }}>
                                                        {{ $department->department }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-8">
                                            <label>Skills</label>
                                            <div id="skills_container" class="mt-2">
                                                <small class="text-muted">Select department first</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card mt-4">
                            <div class="card-header">Documents</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="row">

                                        <div class="form-group mr-2 mb-2 col-md-6">
                                            <label>Photo (jpg,jpeg,png | max:2MB)</label>
                                            <input type="file" name="photo" class="form-control"
                                                placeholder="Photo">
                                        </div>

                                        <div class="form-group mr-2 mb-2 col-md-6">
                                            <label>Resume (pdf,doc,docx | max:4MB)</label>
                                            <input type="file" name="resume" class="form-control"
                                                placeholder="Resume">
                                        </div>

                                        <div class="col-md-12 mt-3 text-end">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const departmentSelect = document.getElementById('department_skills_id');
        const skillsContainer = document.getElementById('skills_container');

        departmentSelect.addEventListener('change', function() {
            const departmentId = this.value;
            skillsContainer.innerHTML = '';

            if (!departmentId) return;

            fetch(`/department-skills/${departmentId}`)
                .then(res => res.json())
                .then(skills => {

                    if (!skills.length) return;

                    skills.forEach(skill => {
                        const label = document.createElement('label');
                        label.classList.add('me-2');

                        const input = document.createElement('input');
                        input.type = 'checkbox';
                        input.name = 'skills[]';
                        input.value = skill;
                        input.classList.add('me-1');

                        label.appendChild(input);
                        label.appendChild(document.createTextNode(skill));

                        skillsContainer.appendChild(label);
                    });
                });
        });
    </script>


</body>

</html>
