@extends('layouts.app')
@section('title', 'Careers')
@section('content')
    <script>
        const selectedSkills = @json(old('skills', $career->skills ?? []));
    </script>

    <div class="container-fluid">

        <form method="POST" action="{{ route('career.update', $career->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">Edit Career</div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3 mb-2">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $career->name) }}">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $career->email) }}">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control"
                                value="{{ old('mobile', $career->mobile) }}">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>City</label>
                            <input type="text" name="city" class="form-control"
                                value="{{ old('city', $career->city) }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control"
                                value="{{ old('address', $career->address) }}">
                        </div>


                        <div class="col-md-3 mb-2">
                            <label>Pincode</label>
                            <input type="text" name="pincode" class="form-control"
                                value="{{ old('pincode', $career->pincode) }}">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>State</label>
                            <select name="state_id" class="form-control">
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('state_id', $career->state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Office Location</label>
                            <select name="office_location" class="form-control">
                                @foreach (['Patna', 'Noida', 'Ranchi', 'Durgapur'] as $loc)
                                    <option value="{{ $loc }}"
                                        {{ old('office_location', $career->office_location) == $loc ? 'selected' : '' }}>
                                        {{ $loc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Department</label>
                            <select name="department_skills_id" id="department_skills_id" class="form-control">
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_skills_id', $career->department_skills_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                @foreach (['active', 'inactive'] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('status', $career->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Skills</label>
                            <div id="skills_container"></div>
                        </div>

                        <hr>

                        <div class="col-md-6 mb-2">
                            <label>Photo (jpg,jpeg,png | max:2MB)</label>
                            <input type="file" name="photo" class="form-control">
                            @if ($career->photo)
                                Previous Photo: <img src="{{ Storage::url($career->photo) }}" alt="Photo"
                                    class="img-thumbnail" width="50">
                            @else
                                No Photo
                            @endif
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Resume (pdf,doc,docx | max:4MB)</label>
                            <input type="file" name="resume" class="form-control">
                            @if ($career->resume)
                                <a href="{{ Storage::url($career->resume) }}" target="_blank">Previous Resume</a>
                            @else
                                No Resume
                            @endif
                        </div>

                        <div class="col-md-12 mt-3">
                            <button class="btn btn-success">Update</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

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

                        if (selectedSkills.includes(skill)) {
                            input.checked = true;
                        }

                        label.appendChild(input);
                        label.appendChild(document.createTextNode(skill));

                        skillsContainer.appendChild(label);
                    });
                });
        });

        if (departmentSelect.value) {
            departmentSelect.dispatchEvent(new Event('change'));
        }
    </script>

@endsection
