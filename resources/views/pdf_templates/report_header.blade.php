<!DOCTYPE html>
<html>
<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .header-container {
            width: 100%;
            padding-bottom: 5px;
            border-bottom: 2px solid #ffcc00;
            margin-bottom: 10px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 5px;
        }
        
        .logo-container img {
            max-width: 100px;
            height: auto;
        }
        
        .company-name {
            text-align: center;
            margin: 2px 0;
            font-weight: bold;
            font-size: 12px;
        }
        
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
            color: #000;
            background-color: #ffcc00;
            padding: 3px;
            border-radius: 3px;
        }
        
        .report-period {
            text-align: center;
            font-size: 10px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo-container">
            <img src="{{ public_path('backend/assets/images/logo-icon.png') }}" alt="Spider Web Logo">
        </div>
        <div class="company-name">خيوط العنكبوت لمكافحة الحشرات</div>
        <div class="company-name">Spider Web For Pest Control</div>
        <div class="report-title">{{ $title }}</div>
        @if($startDate && $endDate)
        <div class="report-period">Period: {{ $startDate }} to {{ $endDate }}</div>
        @endif
    </div>
</body>
</html>
