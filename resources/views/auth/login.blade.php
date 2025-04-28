<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <title>Login - Spider Web</title>
    <link rel="icon" type="image/png" href="{{ asset('backend/assets/images/favicon-32x32.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: #1f1f1f url('/images/page-0001.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
            width: 90%;
            max-width: 450px;
            margin: 2rem auto;
        }

        @media (min-width: 1024px) {
            .login-container {
                margin-right: 8%;
                margin-left: auto;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 1rem auto;
                padding: 2rem;
                width: 90%;
                max-width: none;
            }

            input[type="email"],
            input[type="password"] {
                font-size: 16px; /* Prevents zoom on mobile */
                height: 50px;
            }

            .btn-primary {
                height: 50px;
                font-size: 16px;
            }
        }

        .btn-primary {
            background-color: #b71c1c;
            border: none;
            border-radius: 30px;
            transition: background-color 0.3s;
            padding: 0.75rem 1.5rem;
            width: 100%;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #d32f2f;
        }

        .text-highlight {
            color: #b71c1c;
            font-weight: bold;
        }

        .spider-logo {
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            width: 96px;
            height: 96px;
            object-fit: contain;
        }

        input[type="email"],
        input[type="password"] {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1rem;
            width: 100%;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #b71c1c;
            box-shadow: 0 0 0 2px rgba(183, 28, 28, 0.2);
        }

        label {
            margin-bottom: 0.5rem;
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        @media (max-width: 640px) {
            .spider-logo {
                width: 80px;
                height: 80px;
            }

            h1 {
                font-size: 2rem;
                margin-top: 1rem;
            }

            .login-container {
                padding: 1.5rem;
                margin: 1rem;
                width: auto;
            }

            .remember-forgot {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin: 1rem 0;
            }

            input[type="checkbox"] {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>

<body class="flex justify-center items-center p-4 min-h-screen">
    <div class="login-container">
        <div class="mb-8 text-center">
            <img src="{{ asset('backend/assets/images/logo.png') }}" alt="Spider Web Logo" class="mx-auto spider-logo">
            <h1 class="mt-4 text-3xl font-bold sm:text-4xl text-highlight">Spider Web</h1>
            <p class="mt-2 text-lg">Pest Control Services</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block font-medium">Email Address</label>
                <input id="email" name="email" type="email" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label for="password" class="block font-medium">Password</label>
                <input id="password" name="password" type="password" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>

            @if ($errors->any())
            <div class="text-sm text-red-500">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="flex items-center remember-forgot">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2">
                    <label for="remember" class="text-sm">Remember Me</label>
                </div>
                <a href="{{ route('password.request') }}" class="ml-auto text-sm text-red-500 hover:underline">Forgot Password?</a>
            </div>

            <button type="submit" class="flex justify-center items-center btn-primary">
                Login
            </button>
        </form>
    </div>
</body>

</html>
