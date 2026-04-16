<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class VillaController extends Controller
{
    public function index()
    {
        $villas = Villa::where('is_featured', true)->get();
        return Inertia::render('Welcome', [
            'villas' => $villas
        ]);
    }

    public function show(Villa $villa)
    {
        // Get confirmed booking dates to disable them in the calendar
        $bookedDates = Booking::where('villa_id', $villa->id)
            ->where('status', 'confirmed')
            ->get(['check_in', 'check_out'])
            ->flatMap(function ($booking) {
                $period = new \DatePeriod(
                    $booking->check_in,
                    new \DateInterval('P1D'),
                    $booking->check_out->addDay() // Include checkout day or not depending on policy
                );
                $dates = [];
                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }
                return $dates;
            })
            ->unique()
            ->values();

        return Inertia::render('VillaDetail', [
            'villa' => $villa,
            'bookedDates' => $bookedDates
        ]);
    }

    public function checkout(Villa $villa, Request $request)
    {
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');
        $guests = $request->query('guests', 1);

        // Logic Correction: Default to null if dates are missing or invalid
        if (!$checkIn || !$checkOut || $checkIn === 'null' || $checkOut === 'null') {
            return Inertia::render('Checkout', [
                'villa' => $villa,
                'checkIn' => null,
                'checkOut' => null,
                'guests' => $guests,
                'totalPrice' => 0,
                'nights' => 0,
            ]);
        }

        $totalPrice = 0;
        $startDate = Carbon::parse($checkIn);
        $endDate = Carbon::parse($checkOut);
        $nights = $startDate->diffInDays($endDate);
        
        if ($nights <= 0) {
            return redirect()->back()->withErrors(['dates' => 'Invalid date range selected.']);
        }

        $totalPrice = $villa->price_per_night * $nights;

        return Inertia::render('Checkout', [
            'villa' => $villa,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'guests' => $guests,
            'totalPrice' => $totalPrice,
            'nights' => $nights,
        ]);
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'villa_id' => 'required|exists:villas,id',
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'required|numeric',
            'special_requests' => 'nullable|string',
        ]);

        $booking = Booking::create($validated);

        // WhatsApp Integration Logic
        $this->sendWhatsAppNotification($booking);

        return redirect()->route('home')->with('success', 'Booking successful! We will contact you via WhatsApp.');
    }

    protected function sendWhatsAppNotification(Booking $booking)
    {
        $villa = $booking->villa;
        $message = "New Booking for {$villa->name}!\n\n" .
            "Customer: {$booking->customer_first_name} {$booking->customer_last_name}\n" .
            "Email: {$booking->customer_email}\n" .
            "Phone: {$booking->customer_phone}\n" .
            "Dates: {$booking->check_in->format('d M Y')} to {$booking->check_out->format('d M Y')}\n" .
            "Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n" .
            "Special Requests: " . ($booking->special_requests ?? 'None');

        $phone = '+6285792890791';
        $whatsappUrl = "https://api.whatsapp.com/send?phone={$phone}&text=" . urlencode($message);

        // In a real scenario, you'd use an API like Twilio or a similar service.
        // For this task, we can just return the URL or log it, but the requirement says "automatically generate and send".
        // Since we can't "send" directly from a server without an API, I'll provide a way for the user to trigger it or simulate it.
        // Usually, "send" means the system does it. I'll log it for now and mention it.
        Log::info("WhatsApp Message would be sent to {$phone}: " . $message);
        
        // However, the requirement is "Upon submitting the booking form, the system should automatically generate and send".
        // This often means redirecting the user to WhatsApp with the message pre-filled.
        // Let's store the URL and pass it to the frontend if needed.
        session(['whatsapp_url' => $whatsappUrl]);
    }
}
