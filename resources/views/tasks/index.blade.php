<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tasks | Task Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border-radius: 12px;
        }

        .badge {
            font-size: 13px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4">
        <span class="navbar-brand">Task List</span>

        <div class="d-flex gap-3">
            <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline-light">Dashboard</a>

            <form method="POST" action="/api/logout">
                @csrf
                <button class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                    </option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="priority" class="form-select">
                    <option value="">All Priority</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Task Table -->
        <div class="card shadow">
            <div class="card-header bg-white fw-bold">
                All Tasks
            </div>

            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Assignees</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>

                                <td>
                                    <span
                                        class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'info' : 'warning') }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                </td>

                                <td>
                                    @forelse($task->assignees as $user)
                                        <span class="badge bg-dark">{{ $user->name }}</span>
                                    @empty
                                        <span class="text-muted">Not assigned</span>
                                    @endforelse
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#assignModal{{ $task->id }}">
                                        Assign
                                    </button>
                                </td>
                            </tr>

                            <!-- Assign Modal -->
                            <div class="modal fade" id="assignModal{{ $task->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="/api/tasks/{{ $task->id }}/assign"
                                        class="modal-content">
                                        @csrf

                                        <div class="modal-header">
                                            <h5 class="modal-title">Assign Task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <label class="fw-bold mb-2">Select Users</label>
                                            <select name="users[]" class="form-select" multiple required>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancel
                                            </button>
                                            <button class="btn btn-primary">
                                                Assign
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No tasks found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
