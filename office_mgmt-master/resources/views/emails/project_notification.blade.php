<html>
<body>
    <h2>New Project Added</h2>
    <p>Dear {{ $user->name }},</p>
    <p>A new project <strong>{{ $project->name }}</strong> has been added.</p>
    <p>Project Details:</p>
    <ul>
        <li>Name: {{ $project->name }}</li>
        <li>Description: {{ $project->description }}</li>
        <li>Start Date: {{ $project->start_date }}</li>
        <li>End Date: {{ $project->end_date }}</li>
    </ul>
    <p>Please login to your account for more details.</p>
</body>
</html>
