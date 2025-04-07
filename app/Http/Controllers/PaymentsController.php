<?php

namespace App\Http\Controllers;

use App\Models\payments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\NotificationDispatcher;
use Mpdf\Mpdf;
use NumberToWords\NumberToWords;

class PaymentsController extends Controller
{
    use NotificationDispatcher;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the QR code view of the payment
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function qrView($id)
    {
        $payment = payments::with('customer')->findOrFail($id);
        return view('payments.qr_view', compact('payment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:clients,id',
            'contract_id' => 'required|exists:contracts,id',
            'payment_amount' => 'required|numeric',
            'due_date' => 'required|date',
            'payment_description' => 'nullable|string'
        ]);

        // Generate unique invoice number (YearMonthDay-RandomNumber)
        $invoiceNumber = Carbon::now()->format('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $payment = payments::create([
            'customer_id' => $request->customer_id,
            'contract_id' => $request->contract_id,
            'payment_amount' => $request->payment_amount,
            'due_date' => $request->due_date,
            'payment_status' => 'unpaid',
            'payment_description' => $request->payment_description,
            'invoice_number' => $invoiceNumber,
        ]);

        // Notify client, sales, and finance about the new payment
        $notificationData = [
            'title' => 'New Payment Created',
            'message' => 'A new payment of ' . $payment->payment_amount . ' SAR has been created with due date ' . $payment->due_date,
            'type' => 'info',
            'url' => route('payment.show', $payment->id),
            'priority' => 'normal',
        ];

        // Different URLs for different roles
        $roleUrls = [
            'client' => route('client.contract.visits', $payment->contract_id),
            'sales' => route('payment.show', $payment->id),
            'finance' => route('payment.show', $payment->id)
        ];
        $this->notifyRoles(['client', 'sales', 'finance'], $notificationData, $payment->customer_id, null, $roleUrls);

        return redirect()->route('payment.show', $payment->id)
            ->with('success', 'تم إنشاء الدفعة والفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = payments::with(['customer', 'contract'])->findOrFail($id);
        $words = $this->convertNumberToArabicWords($payment->payment_amount);
        return view('payments.view', compact('payment', 'words'));
    }

    function convertNumberToArabicWords($number)
    {
        $numberToWords = new NumberToWords();
        $numberTransformerArabic = $numberToWords->getNumberTransformer('ar');
        $numberTransformerEnglish = $numberToWords->getNumberTransformer('en');
        $arabicWords = $numberTransformerArabic->toWords($number);
        $englishWords = $numberTransformerEnglish->toWords($number);
        return ['arabic' => $arabicWords, 'english' => $englishWords];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(payments $payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, payments $payments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(payments $payments)
    {
        //
    }

    /**
     * Mark a payment as paid
     */
    public function markAsPaid($id)
    {
        try {
            $payment = payments::findOrFail($id);
            $payment->update([
                'payment_status' => 'paid',
                'payment_method' => request('payment_method'),
                'paid_at' => now(),
            ]);
            // Update the corresponding invoice status to 'paid'
            $invoice = $payment->invoice; // Assuming the payment model has an invoice relationship
            if ($invoice) {
                $invoice->status = 'paid'; // Set invoice status to paid
                $invoice->payment_date = now()->format('Y-m-d'); // Update payment date
                $invoice->save();
            }
            
            // notify the client, sales and finance
            $notificationData = [
                'title' => 'Payment Marked as Paid',
                'message' => 'Payment of ' . $payment->payment_amount . ' SAR has been paid',
                'type' => 'info',
                'priority' => 'normal',
            ];

            // Different URLs for different roles
            $roleUrls = [
                'client' => route('payment.show', $payment->id),
                'sales' => route('payment.show', $payment->id),
                'finance' => route('payment.show', $payment->id)
            ];
            $this->notifyRoles(['client', 'sales', 'finance'], $notificationData, $payment->customer_id, $payment->sales_id, $roleUrls);

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details for modal display
     */
    public function getDetails($id)
    {
        try {
            $payment = payments::findOrFail($id);
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'payment' => [
                    'payment_amount' => $payment->payment_amount,
                    'due_date' => $payment->due_date,
                    'payment_status' => $payment->payment_status,
                    'paid_at' => $payment->paid_at,
                    'invoice_number' => $payment->invoice_number,
                    'payment_method' => $payment->payment_method,
                    'payment_description' => $payment->payment_description
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Payment details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payment details'
            ], 500);
        }
    }

    /**
     * Generate PDF for payment invoice
     */
    public function generatePDF($id)
    {
        $payment = payments::with('contract.customer')->findOrFail($id);

        // Generate amount in words
        $amount = $payment->payment_amount;
        $words = $this->convertNumberToArabicWords($amount);
        // Generate QR code content - exactly matching the web view
        $qrCodeContent = implode("\n", [
            $payment->company->name ?? 'Spider Web Co.',
            'TAX: ' . ($payment->company->tax_number ?? '310152424500003'),
            'Invoice: ' . str_pad($payment->invoice_number ?? $payment->id, 5, '0', STR_PAD_LEFT),
            'Date: ' . \Carbon\Carbon::parse($payment->invoice_date ?? $payment->created_at)->format('Y-m-d'),
            'Total: ' . number_format($payment->payment_amount + ($payment->payment_amount * 0.15), 2) . ' SAR'
        ]);

        // Generate QR code as SVG (doesn't require Imagick)
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
            ->errorCorrection('H')
            ->margin(1)
            ->format('svg')
            ->generate($qrCodeContent);

        // Initialize mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'default_font' => 'cairo'
        ]);

        // Generate PDF content
        $html = view('pdf_templates.payment_invoice_pdf', compact(
            'payment',
            'words',
            'qrCode'
        ))->render();

        // Write PDF content
        $mpdf->WriteHTML($html);

        // Return the PDF as a download
        return $mpdf->Output('Invoice_' . $payment->payment_number . '.pdf', 'D');
    }
}
