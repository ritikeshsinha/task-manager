<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Task Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border-radius: 12px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4">
        <span class="navbar-brand">Task Manager Dashboard</span>
        <div class="d-flex gap-2">
            <a href="{{ url('/tasks') }}" class="btn btn-sm btn-outline-light">Tasks</a>
            <form method="POST" action="/api/logout">
                @csrf
                <button class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">

            <div class="col-md-2">
                <div class="card shadow text-center p-3">
                    <h6>Total Tasks</h6>
                    <div class="stat-number text-primary">{{ $totalTasks }}</div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow text-center p-3">
                    <h6>Pending</h6>
                    <div class="stat-number text-warning">{{ $pendingTasks }}</div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow text-center p-3">
                    <h6>In Progress</h6>
                    <div class="stat-number text-info">{{ $inProgressTasks }}</div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow text-center p-3">
                    <h6>Completed</h6>
                    <div class="stat-number text-success">{{ $completedTasks }}</div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow text-center p-3">
                    <h6>Overdue</h6>
                    <div class="stat-number text-danger">{{ $overdueTasks }}</div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow text-center p-3">
                    <h6>Completion Rate</h6>
                    <div class="stat-number text-secondary">{{ $completionRate }}%</div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-white fw-bold">Recent Notifications</div>
            <div class="card-body">
                <ul class="list-group">
                    @forelse($notifications as $note)
                        <li class="list-group-item">
                            {{ $note->data['title'] ?? 'Task Notification' }} -
                            <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No notifications</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-white fw-bold">User Performance</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Tasks Assigned</th>
                            <th>Tasks Completed</th>
                            <th>Completion %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usersPerformance as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->totalAssigned }}</td>
                                <td>{{ $u->completedTasks }}</td>
                                <td>{{ $u->totalAssigned > 0 ? round(($u->completedTasks / $u->totalAssigned) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Latest Tasks Table -->
        <div class="card shadow mb-4">
            <div class="card-header bg-white fw-bold">
                Latest Tasks
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'info' : 'warning') }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($task->priority) }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No tasks available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header fw-bold bg-white">
                        Task Status Overview
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header fw-bold bg-white">
                        Task Priority Overview
                    </div>
                    <div class="card-body">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row g-4">
            <div class="col-md-6">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="priorityChart"></canvas>
            </div>
        </div> --}}

    </div>

    <script>
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Pending', 'In Progress', 'Completed', 'Overdue'],
                datasets: [{
                    data: [{{ $statusChart['pending'] }}, {{ $statusChart['in_progress'] }},
                        {{ $statusChart['completed'] }}, {{ $statusChart['overdue'] }}
                    ],
                    backgroundColor: ['#FFC107', '#17A2B8', '#28A745', '#DC3545']
                }]
            }
        });

        new Chart(document.getElementById('priorityChart'), {
            type: 'bar',
            data: {
                labels: ['Low', 'Medium', 'High'],
                datasets: [{
                    data: [{{ $priorityChart['low'] }}, {{ $priorityChart['medium'] }},
                        {{ $priorityChart['high'] }}
                    ],
                    backgroundColor: ['#6C757D', '#007BFF', '#DC3545']
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>


    {{-- <!-- Chart JS -->
    <script>
        // Status Pie Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    label: 'Tasks',
                    data: [
                        {{ $statusChart['pending'] }},
                        {{ $statusChart['in_progress'] }},
                        {{ $statusChart['completed'] }}
                    ],
                    backgroundColor: ['#FFC107', '#17A2B8', '#28A745']
                }]
            }
        });

        // Priority Bar Chart
        new Chart(document.getElementById('priorityChart'), {
            type: 'bar',
            data: {
                labels: ['Low', 'Medium', 'High'],
                datasets: [{
                    label: 'Priority',
                    data: [
                        {{ $priorityChart['low'] }},
                        {{ $priorityChart['medium'] }},
                        {{ $priorityChart['high'] }}
                    ],
                    backgroundColor: ['#6C757D', '#007BFF', '#DC3545']
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script> --}}

</body>

</html>
