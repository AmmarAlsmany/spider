<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ReportsController extends Controller
{
    /**
     * Generate PDF for reports
     */
    public function generatePDF(Request $request)
    {
        // Get report type and parameters from request
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $title = $request->input('title');
        $htmlContent = $request->input('html_content');
        
        // Initialize mPDF with specific settings
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'default_font' => 'cairo'
        ]);
        
        // Create a complete HTML document with proper structure
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap");
                body {
                    font-family: "Cairo", sans-serif;
                    margin: 0;
                    padding: 0;
                    font-size: 12px;
                }
                .header {
                    text-align: center;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #ffcc00;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 100px;
                    margin: 0 auto;
                    display: block;
                }
                .company-name {
                    font-weight: bold;
                    margin: 5px 0;
                    font-size: 14px;
                }
                .report-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 10px 0;
                    background-color: #ffcc00;
                    padding: 5px;
                    color: #000;
                }
                .report-period {
                    font-size: 12px;
                    margin-bottom: 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                    padding: 8px;
                    text-align: center;
                }
                td {
                    padding: 8px;
                    text-align: left;
                }
                .text-center {
                    text-align: center;
                }
                .text-right {
                    text-align: right;
                }
                .fw-bold {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . public_path('backend/assets/images/logo-icon.png') . '" alt="Spider Web Logo" class="logo">
                <div class="company-name">خيوط العنكبوت لمكافحة الحشرات</div>
                <div class="company-name">Spider Web For Pest Control</div>
                <div class="report-title">' . $title . '</div>';
        
        if ($startDate && $endDate) {
            $html .= '<div class="report-period">Period: ' . $startDate . ' to ' . $endDate . '</div>';
        }
        
        $html .= '</div>
            <div class="content">' . $htmlContent . '</div>
        </body>
        </html>';
        
        // Write the complete HTML to the PDF
        $mpdf->WriteHTML($html);
        
        // Return the PDF as a download
        return $mpdf->Output($reportType . '_report.pdf', 'D');
    }
}
