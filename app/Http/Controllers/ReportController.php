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
        $query = User::where('role', 'resident')->with('purok');
        
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
     * Show purok leaders list preview
     */
    public function purokLeaders(Request $request)
    {
        $query = User::where('role', 'purok_president')->with('purok');
        
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
        $query = User::where('role', 'purok_president')->with('purok')->orderBy('last_name');
        
        // If specific IDs are provided, filter by them
        if ($request->has('leader_ids') && !empty($request->leader_ids)) {
            $query->whereIn('id', $request->leader_ids);
        }
        
        $leaders = $query->get();
        $pdf = PDF::loadView('reports.pdf.purok-leaders', compact('leaders'));
        return $pdf->download('purok-leaders-list-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show purok clearance requests preview
     */
    public function purokClearance()
    {
        $requests = ResidentRequest::with(['user', 'purok', 'purokApprover', 'barangayApprover'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.preview.purok-clearance', compact('requests'));
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
     * Show incident reports preview
     */
    public function incidentReports()
    {
        $reports = IncidentReport::with(['user', 'purok'])
            ->orderBy('created_at', 'desc')
            ->get();

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
}
