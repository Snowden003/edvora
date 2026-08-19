@extends('layouts.app')

@section('title', 'How Scoring Works - Edvora Tech')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-3">How Scoring Works</h1>
                <p class="lead text-muted mb-5">
                    Our gamification system rewards positive behavior and helps you stay on track.
                    Earn points for attendance, assignments, and participation — lose points for absences and missed work.
                </p>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="fw-bold mb-0"><i class="bi bi-stars me-2 text-warning"></i>Ways to Earn Points</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($rules->where('default_score', '>', 0) as $rule)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>{{ $rule->label }}</strong>
                                    <div class="small text-muted">{{ $rule->description }}</div>
                                </div>
                                <span class="badge bg-success">+{{ $rule->default_score }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="fw-bold mb-0"><i class="bi bi-dash-circle me-2 text-danger"></i>Things That Deduct Points</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($rules->where('default_score', '<', 0) as $rule)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>{{ $rule->label }}</strong>
                                    <div class="small text-muted">{{ $rule->description }}</div>
                                </div>
                                <span class="badge bg-danger">{{ $rule->default_score }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-primary"></i>Leaderboard & Ranking</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Your rank is calculated from your total score. The leaderboard can be filtered by course or time range.
                            Stay consistent, attend classes on time, complete assignments, and participate actively to climb the ranks.
                        </p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Attendance and punctuality matter most.</li>
                            <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Complete every assignment on time.</li>
                            <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Participate in class discussions.</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i>Check your dashboard regularly for updates.</li>
                        </ul>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('leaderboard') }}" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-bar-chart me-2"></i>View Leaderboard
                    </a>
                    @auth
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-grid me-2"></i>My Dashboard
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
