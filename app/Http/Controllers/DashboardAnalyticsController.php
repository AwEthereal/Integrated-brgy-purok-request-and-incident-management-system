<?php

namespace App\Http\Controllers;

use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Purok;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsController extends Controller
{
    public function clearances(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $period = $request->query('period', 'monthly'); // monthly|quarterly|annual
        $group = $request->query('group', 'total'); // total|per_purok
        $year = (int) $request->query('year', now()->year);
        $purokId = $request->query('purok_id'); // optional filter for time-series

        $clearanceTypes = [
            'barangay_clearance',
            'business_clearance',
            'certificate_of_residency',
            'certificate_of_indigency',
        ];

        $base = ServiceRequest::query()->whereIn('form_type', $clearanceTypes);

        // Scope by role
        if ($user->role === 'purok_leader') {
            $base->where('purok_id', $user->purok_id);
            $group = 'total'; // disallow per_purok for leaders
        }

        // Optional purok filter for time-series (officials/secretary/captain/admin)
        if ($group === 'total' && $purokId && $purokId !== 'all' && in_array($user->role, ['secretary', 'barangay_kagawad', 'barangay_captain', 'admin', 'sk_chairman'])) {
            $base->where('purok_id', (int) $purokId);
        }

        // Per-purok grouping (allowed for officials/secretary/captain/admin)
        if ($group === 'per_purok' && in_array($user->role, ['secretary', 'barangay_kagawad', 'barangay_captain', 'admin', 'sk_chairman'])) {
            $rows = (clone $base)
                ->whereYear('created_at', $year)
                ->select('purok_id', DB::raw('COUNT(*) as count'))
                ->groupBy('purok_id')
                ->get();

            $purokNames = Purok::whereIn('id', $rows->pluck('purok_id')->all())
                ->pluck('name', 'id');

            $labels = $rows->map(fn($r) => $purokNames[$r->purok_id] ?? ('Purok #' . $r->purok_id));
            $data = $rows->pluck('count');

            return response()->json([
                'labels' => $labels->values(),
                'datasets' => [[
                    'label' => 'Clearance Requests ('.$year.')',
                    'data' => $data->values(),
                ]],
            ]);
        }

        // Time series (monthly/quarterly/annual)
        $labels = [];
        $data = [];
        $now = now();

        if ($period === 'monthly') {
            for ($i = 11; $i >= 0; $i--) {
                $dt = $now->copy()->subMonths($i);
                $count = (clone $base)
                    ->whereYear('created_at', $dt->year)
                    ->whereMonth('created_at', $dt->month)
                    ->count();
                $labels[] = $dt->format('M Y');
                $data[] = $count;
            }
        } elseif ($period === 'quarterly') {
            // Last 4 quarters
            $currentQuarter = (int) ceil($now->month / 3);
            $yearQ = $now->year;
            for ($i = 3; $i >= 0; $i--) {
                $q = $currentQuarter - $i;
                $y = $yearQ;
                while ($q <= 0) { $q += 4; $y -= 1; }
                $start = Carbon::create($y, ($q - 1) * 3 + 1, 1)->startOfMonth();
                $end = $start->copy()->addMonths(2)->endOfMonth();
                $count = (clone $base)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $labels[] = 'Q'.$q.' '.$y;
                $data[] = $count;
            }
        } else { // annual
            for ($i = 4; $i >= 0; $i--) {
                $y = $now->copy()->subYears($i)->year;
                $count = (clone $base)->whereYear('created_at', $y)->count();
                $labels[] = (string) $y;
                $data[] = $count;
            }
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Clearance Requests',
                'data' => $data,
            ]],
        ]);
    }

    public function incidents(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Allow officials/secretary/captain/admin to view incidents analytics
        if (!in_array($user->role, ['secretary', 'barangay_kagawad', 'barangay_captain', 'admin', 'sk_chairman'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $period = $request->query('period', 'monthly'); // monthly|quarterly|annual
        $group = $request->query('group', 'total'); // total|per_type
        $year = (int) $request->query('year', now()->year);

        $base = IncidentReport::query();

        if ($group === 'per_type') {
            $rows = (clone $base)
                ->whereYear('created_at', $year)
                ->select('incident_type', DB::raw('COUNT(*) as count'))
                ->groupBy('incident_type')
                ->get();

            $labels = $rows->pluck('incident_type')->map(fn($t) => $t ? ucwords(str_replace('_',' ', $t)) : 'Unknown');
            $data = $rows->pluck('count');

            return response()->json([
                'labels' => $labels->values(),
                'datasets' => [[
                    'label' => 'Reported Incidents ('.$year.')',
                    'data' => $data->values(),
                ]],
            ]);
        }

        // Time series for incidents
        $labels = [];
        $data = [];
        $now = now();

        if ($period === 'monthly') {
            for ($i = 11; $i >= 0; $i--) {
                $dt = $now->copy()->subMonths($i);
                $count = (clone $base)
                    ->whereYear('created_at', $dt->year)
                    ->whereMonth('created_at', $dt->month)
                    ->count();
                $labels[] = $dt->format('M Y');
                $data[] = $count;
            }
        } elseif ($period === 'quarterly') {
            $currentQuarter = (int) ceil($now->month / 3);
            $yearQ = $now->year;
            for ($i = 3; $i >= 0; $i--) {
                $q = $currentQuarter - $i;
                $y = $yearQ;
                while ($q <= 0) { $q += 4; $y -= 1; }
                $start = Carbon::create($y, ($q - 1) * 3 + 1, 1)->startOfMonth();
                $end = $start->copy()->addMonths(2)->endOfMonth();
                $count = (clone $base)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $labels[] = 'Q'.$q.' '.$y;
                $data[] = $count;
            }
        } else { // annual
            for ($i = 4; $i >= 0; $i--) {
                $y = $now->copy()->subYears($i)->year;
                $count = (clone $base)->whereYear('created_at', $y)->count();
                $labels[] = (string) $y;
                $data[] = $count;
            }
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Reported Incidents',
                'data' => $data,
            ]],
        ]);
    }
}
