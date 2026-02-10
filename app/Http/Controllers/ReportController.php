<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Purok;
use App\Models\Request as ResidentRequest;
use App\Models\IncidentReport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Show residents list preview
     */
    public function residents(Request $request)
    {
        $user = Auth::user();
        // For secretaries, show RBI Resident Records across all puroks, with Active/Archived status
        if ($user && in_array($user->role, ['secretary', 'barangay_captain'], true)) {
            $search = (string) $request->get('search', (string) $request->get('q', ''));
            $recordsQuery = \App\Models\ResidentRecord::query()->with(['purok']);

            if ($request->filled('purok_id')) {
                $recordsQuery->where('purok_id', $request->purok_id);
            }

            if ($search !== '') {
                $recordsQuery->where(function($sub) use ($search) {
                    $sub->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('philsys_card_no', 'like', "%{$search}%")
                        ->orWhere('residence_address', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            }

            $records = $recordsQuery->orderBy('last_name')->paginate(15)->withQueryString();
            $purokIds = $records->getCollection()->pluck('purok_id')->filter()->unique()->values();
            $leadersByPurok = User::where('role', 'purok_leader')
                ->whereIn('purok_id', $purokIds)
                ->get()
                ->keyBy('purok_id');
            $puroks = Purok::orderBy('name')->get();
            return view('reports.preview.residents-rbi', [
                'records' => $records,
                'puroks' => $puroks,
                'search' => $search,
                'leadersByPurok' => $leadersByPurok,
            ]);
        }

        // Default: show Users-based residents list
        $query = User::with('purok')->where(function($q){
            $q->where('role', 'resident')
              ->orWhereHas('residentRecords');
        });
        
        if ($request->filled('purok_id')) {
            $query->where('purok_id', $request->purok_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        
        $residents = $query->orderBy('last_name')->get();
        $puroks = Purok::orderBy('name')->get();
        
        return view('reports.preview.residents', compact('residents', 'puroks'));
    }
    
    /**
     * Show resident profile
     */
    public function showResident(User $user)
    {
        $user->load('purok');
        return view('reports.show.resident', compact('user'));
    }

    /**
     * Download residents report
     */
    public function downloadResidents(Request $request)
    {
        $query = User::where('role', 'resident')->with('purok')->orderBy('last_name');
        
        // If specific IDs are provided, filter by them
        if ($request->has('resident_ids') && !empty($request->resident_ids)) {
            $query->whereIn('id', $request->resident_ids);
        }
        
        $residents = $query->get();
        $pdf = PDF::loadView('reports.pdf.residents', compact('residents'));
        return $pdf->download('residents-list-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Preview residents (all or selected via ?ids=1,2,3)
     */
    public function previewResidents(Request $request)
    {
        $idsParam = (string) $request->query('ids', '');
        $ids = array_filter(array_map('intval', array_filter(explode(',', $idsParam))));

        $query = User::where('role', 'resident')->with('purok')->orderBy('last_name');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $residents = $query->get();

        return view('reports.specific.residents', compact('residents'));
    }

    /**
     * Preview RBI residents (all or selected via ?ids=1,2,3) as PDF stream
     */
    public function previewResidentsRbi(Request $request)
    {
        $idsParam = (string) $request->query('ids', '');
        $ids = array_filter(array_map('intval', array_filter(explode(',', $idsParam))));

        $query = \App\Models\ResidentRecord::query()->with(['purok'])->orderBy('last_name');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $records = $query->get();

        $purokIds = $records->pluck('purok_id')->filter()->unique()->values();
        $leadersByPurok = User::where('role', 'purok_leader')
            ->whereIn('purok_id', $purokIds)
            ->get()
            ->keyBy('purok_id');

        $pdf = PDF::loadView('reports.pdf.residents-rbi', [
            'records' => $records,
            'leadersByPurok' => $leadersByPurok,
        ]);

        return $pdf->stream('residents-rbi-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show purok leaders list preview
     */
    public function purokLeaders(Request $request)
    {
        $query = User::where('role', 'purok_leader')->with(['purok', 'latestResidentRecord']);
        
        // Filter by purok if selected
        if ($request->filled('purok_id')) {
            $query->where('purok_id', $request->purok_id);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        
        $leaders = $query->orderBy('last_name')->get();
        $puroks = Purok::orderBy('name')->get();

        return view('reports.preview.purok-leaders', compact('leaders', 'puroks'));
    }
    
    /**
     * Show purok leader profile
     */
    public function showLeader(User $user)
    {
        $user->load('purok');
        return view('reports.show.leader', compact('user'));
    }

    /**
     * Download purok leaders report
     */
    public function downloadPurokLeaders(Request $request)
    {
        $query = User::where('role', 'purok_leader')->with(['purok', 'latestResidentRecord'])->orderBy('last_name');
        
        // If specific IDs are provided, filter by them
        if ($request->has('leader_ids') && !empty($request->leader_ids)) {
            $query->whereIn('id', $request->leader_ids);
        }
        
        $leaders = $query->get();
        $pdf = PDF::loadView('reports.pdf.purok-leaders', compact('leaders'));
        return $pdf->download('purok-leaders-list-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Preview purok leaders (all or selected via ?ids=1,2,3)
     */
    public function previewPurokLeaders(Request $request)
    {
        $idsParam = (string) $request->query('ids', '');
        $ids = array_filter(array_map('intval', array_filter(explode(',', $idsParam))));

        $query = User::where('role', 'purok_leader')->with(['purok', 'latestResidentRecord'])->orderBy('last_name');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $leaders = $query->get();

        return view('reports.specific.purok-leaders', compact('leaders'));
    }

    /**
     * Show purok clearance requests preview
     */
    public function purokClearance(Request $request)
    {
        $query = ResidentRequest::with(['user', 'purok', 'purokApprover', 'barangayApprover'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('purok_id')) {
            $query->where('purok_id', $request->purok_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('purok', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->paginate(15)->withQueryString();
        $puroks = Purok::orderBy('name')->get();

        return view('reports.preview.purok-clearance', compact('requests', 'puroks'));
    }

    /**
     * Download purok clearance report
     */
    public function downloadPurokClearance(Request $request)
    {
        $query = ResidentRequest::with(['user', 'purok', 'purokApprover', 'barangayApprover'])
            ->orderBy('created_at', 'desc');
        
        // If specific IDs are provided, filter by them
        if ($request->has('request_ids') && !empty($request->request_ids)) {
            $query->whereIn('id', $request->request_ids);
        }
        
        $requests = $query->get();
        $pdf = PDF::loadView('reports.pdf.purok-clearance', compact('requests'));
        return $pdf->download('purok-clearance-requests-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Preview purok clearance report (all or selected via ?ids=1,2,3)
     */
    public function previewPurokClearance(Request $request)
    {
        $idsParam = (string) $request->query('ids', '');
        $ids = array_filter(array_map('intval', array_filter(explode(',', $idsParam))));

        $query = ResidentRequest::with(['user', 'purok', 'purokApprover', 'barangayApprover'])
            ->orderBy('created_at', 'desc');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $requests = $query->get();

        $pdf = PDF::loadView('reports.pdf.purok-clearance', compact('requests'));
        return $pdf->stream('purok-clearance-requests-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show incident reports preview
     */
    public function incidentReports()
    {
        $query = IncidentReport::with(['user', 'purok'])->orderBy('created_at', 'desc');

        if (request()->filled('incident_type')) {
            $query->where('incident_type', request('incident_type'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('search')) {
            $search = (string) request('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('reporter_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('reports.preview.incident-reports', compact('reports'));
    }

    /**
     * Download incident reports
     */
    public function downloadIncidentReports(Request $request)
    {
        $query = IncidentReport::with(['user', 'purok'])->orderBy('created_at', 'desc');
        
        // If specific IDs are provided, filter by them
        if ($request->has('incident_ids') && !empty($request->incident_ids)) {
            $query->whereIn('id', $request->incident_ids);
        }
        
        $reports = $query->get();
        $pdf = PDF::loadView('reports.pdf.incident-reports', compact('reports'));
        return $pdf->download('incident-reports-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Preview incident reports (all or selected via ?ids=1,2,3)
     */
    public function previewIncidentReports(Request $request)
    {
        $idsParam = (string) $request->query('ids', '');
        $ids = array_filter(array_map('intval', array_filter(explode(',', $idsParam))));

        $query = IncidentReport::with(['user', 'purok'])->orderBy('created_at', 'desc');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $reports = $query->get();

        $pdf = PDF::loadView('reports.pdf.incident-reports', compact('reports'));
        return $pdf->stream('incident-reports-' . now()->format('Y-m-d') . '.pdf');
    }
}
