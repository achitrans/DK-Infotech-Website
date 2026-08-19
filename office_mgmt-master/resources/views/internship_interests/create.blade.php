<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Internship Interest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
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

                <form action="{{ route('internship-interests.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="card mb-3">
                        <div class="card-header">Personal Information</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" name="phone" class="form-control"
                                        value="{{ old('phone') }}" required pattern="[6-9][0-9]{9}" maxlength="10"
                                        title="Please enter a valid 10-digit Indian mobile number.">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select" required>
                                        @foreach ($types as $val => $label)
                                            <option value="{{ $val }}"
                                                @if (old('type') == $val) selected @endif>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="card-header">Educational Information</div>

                            <!-- Student Type -->
                            <div class="col-md-12 mb-3 mt-2">
                                <label class="form-label">Student Type</label>
                                <select name="student_type" id="student_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="college" {{ old('student_type') == 'college' ? 'selected' : '' }}>College Going</option>
                                    <option value="personal" {{ old('student_type') == 'personal' ? 'selected' : '' }}>Personal Skill Development</option>
                                </select>
                            </div>

                            <!-- College Fields -->
                            <div id="collegeFields" style="display:none;">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Graduation Course</label>
                                        <select name="graduation_course" class="form-select">
                                            <option value="">Select Course</option>
                                            @foreach ($graduationCourse as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('graduation_course') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->course_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current Semester</label>

                                        <div class="d-flex flex-wrap gap-2">
                                            @for ($i = 1; $i <= 8; $i++)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="semester"
                                                        value="{{ $i }}" id="sem{{ $i }}"
                                                        {{ old('semester') == $i ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sem{{ $i }}">
                                                        {{ $i }} Semester
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 ">
                                        <label class="form-label">College Name</label>
                                        <input type="text" name="college" class="form-control"
                                            value="{{ old('college') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3 ">
                                        <label class="form-label">Roll Number</label>
                                        <input type="text" name="roll_no" class="form-control"
                                            value="{{ old('roll_no') }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Select Relation</label>
                                        <select name="parent_relation" class="form-select">
                                            <option value="S/O"
                                                {{ old('parent_relation') == 'S/O' ? 'selected' : '' }}>S/O</option>
                                            <option value="D/O"
                                                {{ old('parent_relation') == 'D/O' ? 'selected' : '' }}>D/O</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3 ">
                                        <label class="form-label">Parent Name</label>
                                        <input type="text" name="parent_name" class="form-control"
                                            value="{{ old('parent_name') }}" required>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="row px-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Position / Role</label>
                                <select name="position" class="form-select">
                                    <option value="">Select Internship Role</option>

                                    @foreach (\App\Models\InternshipInterest::$internship_role as $role)
                                        <option value="{{ $role }}"
                                            {{ old('position') == $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row date-fields-row">
                            <div class="col-md-6 mb-3  date-field">
                                <label class="form-label">Preferred Start Date</label>
                                <input type="date" name="date_of_joining" class="form-control"
                                    min="{{ date('Y-m-d') }}" value="{{ old('date_of_joining') }}" required>

                            </div>
                            </div>
                        </div>

                    </div>

            {{-- <div class="card mb-3">
                        <div class="card-header">Education & Availability</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Degree</label>
                                    <input type="text" name="degree" class="form-control"
                                        value="{{ old('degree') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">University</label>
                                    <input type="text" name="university" class="form-control"
                                        value="{{ old('university') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Graduation Year</label>
                                    <input type="number" name="graduation_year" class="form-control"
                                        value="{{ old('graduation_year') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Availability (weeks)</label>
                                    <input type="number" name="availability_weeks" class="form-control"
                                        value="{{ old('availability_weeks') }}">
                                </div>
                            </div>
                        </div>
                    </div> --}}

            {{-- <div class="card mb-3">
                        <div class="card-header">Skills & Links</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Skills</label>
                                <textarea name="skills" rows="4" class="form-control">{{ old('skills') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Portfolio Link</label>
                                    <input type="url" name="portfolio_link" class="form-control"
                                        value="{{ old('portfolio_link') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">GitHub</label>
                                    <input type="url" name="github_link" class="form-control"
                                        value="{{ old('github_link') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" name="linkedin" class="form-control"
                                        value="{{ old('linkedin') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea name="notes" rows="4" class="form-control">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div> --}}

            <div class="card mb-3">
                <div class="card-header">Resume & Consent</div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Resume (PDF/DOC)</label>
                            <input type="file" name="resume_file" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">How did you hear about us?</label>
                            <select name="source" class="form-select">
                                @foreach ($sources as $val => $label)
                                    <option value="{{ $val }}"
                                        @if (old('source') == $val) selected @endif>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="consent" value="1"
                            id="consent" @if (old('consent')) checked @endif>
                        <label class="form-check-label" for="consent">I consent to my data being stored for
                            application processing.</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                    data-bs-target="#resumeModal">
                    Resume Dropped Transaction
                </button>
                <button class="btn btn-primary">Submit Application</button>
            </div>

            </form>
        </div>
    </div>
    </div>

    <!-- Resume Transaction Modal -->
    <div class="modal fade" id="resumeModal" tabindex="-1" aria-labelledby="resumeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('internship-interests.resume') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="resumeModalLabel">Resume Dropped Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required
                                value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required
                                value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" name="phone" class="form-control" required
                                pattern="[6-9][0-9]{9}" maxlength="10"
                                title="Please enter a valid 10-digit Indian mobile number."
                                value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Resume Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const studentType = document.getElementById('student_type');
            const collegeFields = document.getElementById('collegeFields');
            const collegeInput = document.querySelector('input[name="college"]');
            const rollNoInput = document.querySelector('input[name="roll_no"]');
            const parentNameInput = document.querySelector('input[name="parent_name"]');

            function toggleFields() {
                if (studentType.value === 'college') {
                    collegeFields.style.display = 'block';
                    collegeInput.setAttribute('required', 'required');
                    rollNoInput.setAttribute('required', 'required');
                    parentNameInput.setAttribute('required', 'required');
                } else {
                    collegeFields.style.display = 'none';
                    collegeInput.removeAttribute('required');
                    rollNoInput.removeAttribute('required');
                    parentNameInput.removeAttribute('required');
                }
            }

            studentType.addEventListener('change', toggleFields);

            toggleFields();

            @if ($errors->has('resume_error'))
                const resumeModal = new bootstrap.Modal(document.getElementById('resumeModal'));
                resumeModal.show();
            @endif
        });
    </script>
</body>

</html>
