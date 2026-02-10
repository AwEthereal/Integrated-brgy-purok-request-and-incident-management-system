<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\QrCodeHelper;

class KioskController extends Controller
{
    /**
     * Display the kiosk home page
     */
    public function index()
    {
        // Check if there are featured announcements
        $hasNewAnnouncements = Announcement::active()
            ->published()
            ->featured()
            ->exists();

        return view('kiosk.index', compact('hasNewAnnouncements'));
    }

    /**
     * Display barangay information
     */
    public function information()
    {
        return view('kiosk.information');
    }

    /**
     * Display services offered
     */
    public function services()
    {
        // Get service statistics
        $stats = [
            'total_requests' => ServiceRequest::count(),
            'approved_requests' => ServiceRequest::where('status', 'barangay_approved')->count(),
            'total_incidents' => IncidentReport::count(),
            'resolved_incidents' => IncidentReport::where('status', IncidentReport::STATUS_RESOLVED)->count(),
        ];

        return view('kiosk.services', compact('stats'));
    }

    /**
     * Display barangay officials
     */
    public function officials()
    {
        // Get barangay officials
        $officials = User::whereIn('role', ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman'])
            ->select('name', 'role')
            ->get();

        // Get purok leaders
        $purokLeaders = User::where('role', 'purok_leader')
            ->select('name', 'purok_id')
            ->with('purok:id,name')
            ->orderBy('purok_id')
            ->get();

        return view('kiosk.officials', compact('officials', 'purokLeaders'));
    }

    /**
     * Display announcements
     */
    public function announcements()
    {
        // Get active announcements from database
        $announcements = Announcement::active()
            ->published()
            ->with('creator')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kiosk.announcements', compact('announcements'));
    }

    /**
     * Display contact information
     */
    public function contact()
    {
        return view('kiosk.contact');
    }

    /**
     * Display online services QR code
     */
    public function qrCode()
    {
        $websiteUrl = config('app.url');
        $qrCodeSvg = QrCodeHelper::generateWebsiteQr();
        
        return view('kiosk.qr-code', compact('websiteUrl', 'qrCodeSvg'));
    }

    /**
     * Display document requirements
     */
    public function requirements()
    {
        $requirements = [
            'barangay_clearance' => [
                'name' => 'Barangay Clearance',
                'requirements' => [
                    'Valid Government-issued ID',
                    'Purok Clearance',
                    'Cedula (Community Tax Certificate)',
                    '1x1 ID Picture (2 copies)',
                ],
                'fee' => '₱100.00',
                'processing_time' => '1-2 business days'
            ],
            'business_clearance' => [
                'name' => 'Business Clearance',
                'requirements' => [
                    'Valid Government-issued ID',
                    'Purok Clearance',
                    'DTI/SEC Registration',
                    'Business Permit (if renewal)',
                    'Cedula',
                ],
                'fee' => '₱100.00',
                'processing_time' => '1-2 business days'
            ],
            'certificate_of_residency' => [
                'name' => 'Certificate of Residency',
                'requirements' => [
                    'Valid Government-issued ID',
                    'Purok Clearance',
                    'Proof of Residency (Utility Bill)',
                ],
                'fee' => '₱100.00',
                'processing_time' => '1-2 business days'
            ],
            'indigency' => [
                'name' => 'Certificate of Indigency',
                'requirements' => [
                    'Valid Government-issued ID',
                    'Purok Clearance',
                    'Proof of Income (if applicable)',
                ],
                'fee' => '₱70.00',
                'processing_time' => '1-2 business days'
            ],
        ];

        return view('kiosk.requirements', compact('requirements'));
    }

    /**
     * Reset kiosk session (for idle timeout)
     */
    public function reset()
    {
        return redirect()->route('kiosk.index');
    }
}
