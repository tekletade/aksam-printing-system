<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; }
        h2 { color: #666; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #4a5568; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .summary-box {
            background-color: #f7fafc;
            border: 1px solid #cbd5e0;
            padding: 15px;
            margin-bottom: 20px;
            display: inline-block;
            width: 200px;
            margin-right: 10px;
        }
        .summary-label { font-size: 14px; color: #718096; }
        .summary-value { font-size: 20px; font-weight: bold; color: #2d3748; }
        .footer { margin-top: 30px; text-align: center; color: #a0aec0; }
    </style>
</head>
<body>
    <h1>Attendance Report</h1>

    <p>
        Period: {{ \Carbon\Carbon::parse($filters['from_date'])->format('M d, Y') }} -
        {{ \Carbon\Carbon::parse($filters['to_date'])->format('M d, Y') }}
    </p>

    <h2>Summary Statistics</h2>
    <div style="display: flex; flex-wrap: wrap;">
        <div class="summary-box">
            <div class="summary-label">Total Records</div>
            <div class="summary-value">{{ $summaryStats['total_records'] }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Present</div>
            <div class="summary-value">{{ $summaryStats['present'] }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Absent</div>
            <div class="summary-value">{{ $summaryStats['absent'] }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Late</div>
            <div class="summary-value">{{ $summaryStats['late'] }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Total Hours</div>
            <div class="summary-value">{{ number_format($summaryStats['total_hours'], 1) }}</div>
        </div>
    </div>

    <h2>Detailed Report</h2>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Hours</th>
                <th>Status</th>
                <th>Late</th>
                <th>Overtime</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $record)
            <tr>
                <td>{{ $record->employee?->full_name ?? 'N/A' }}</td>
                <td>{{ $record->attendance_date->format('Y-m-d') }}</td>
                <td>{{ $record->check_in?->format('H:i') ?? '-' }}</td>
                <td>{{ $record->check_out?->format('H:i') ?? '-' }}</td>
                <td>{{ number_format($record->total_hours, 1) }}</td>
                <td>{{ ucfirst($record->status) }}</td>
                <td>{{ $record->is_late ? $record->late_minutes . ' min' : '-' }}</td>
                <td>{{ $record->is_overtime ? number_format($record->overtime_hours, 1) . ' hrs' : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ $generated_at->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
