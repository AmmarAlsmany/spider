<?php

namespace App\Http\Controllers;

use App\Models\payments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{

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

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'تم إنشاء الدفعة والفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = payments::with(['customer', 'contract'])->findOrFail($id);
        return view('payments.view', compact('payment'));
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

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status'
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
}
