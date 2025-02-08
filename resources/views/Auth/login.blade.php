<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: #1f1f1f url('/images/page-0001.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
            width: 90%;
            max-width: 400px;
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
                padding: 1.5rem;
                width: 85%;
            }
        }

        .btn-primary {
            background-color: #b71c1c;
            border: none;
            border-radius: 30px;
            transition: background-color 0.3s;
            padding: 0.75rem 1.5rem;
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
            padding: 0.75rem;
            width: 100%;
            border-radius: 0.5rem;
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
        }

        @media (max-width: 640px) {
            .spider-logo {
                width: 72px;
                height: 72px;
            }

            h1 {
                font-size: 1.75rem;
            }

            .login-container {
                padding: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center">
        <div class="login-container">
            <div class="mb-8 text-center">
                <img src="/images/spider.webp" alt="Spider Web Logo" class="mx-auto w-24 h-24 spider-logo">
                <h1 class="mt-4 text-4xl font-bold text-highlight">Spider Web</h1>
                <p class="text-lg">Pest Control Services</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input id="email" name="email" type="email" required
                        class="block mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input id="password" name="password" type="password" required
                        class="block mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>

                @if ($errors->any())
                <div class="mb-4 text-sm text-red-500">
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <input type="checkbox" name="remember" id="remember" class="mr-1">
                        <label for="remember" class="text-sm">Remember Me</label>
                    </div>
                    <a href="#" class="text-sm text-red-500 hover:underline">Forgot Password?</a>
                </div>

                <button type="submit"
                    class="flex justify-center px-4 py-2 w-full text-sm font-medium text-white btn-primary">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>

</html>
