<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kepegawaian UNMUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .captcha-container {
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            border: 2px solid #ccc;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 3px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .refresh-captcha {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .refresh-captcha:hover {
            transform: rotate(180deg);
        }
    </style>
</head>
<body style="background-image: url('images/bg-unmul.jpg');" class="bg-cover bg-center bg-no-repeat min-h-screen bg-gray-100">

    <main class="flex min-h-screen items-center justify-center p-4">

        <!-- Kartu Login Utama -->
        <div class="relative flex w-full max-w-6xl overflow-hidden rounded-2xl shadow-2xl">

            <div class="w-full bg-gray-400/90 p-8 sm:p-15 lg:w-2/2">
                <div class="text-left">
                    <h1 class="text-3xl font-bold py-7">Halaman Login</h1>

                    <form action="#" method="POST" onsubmit="return validateCaptcha(event)">
                        <!-- CSRF Token (Laravel) -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <!-- NIP Field -->
                        <input type="email" name="email" placeholder="NIP" required
                               class="w-full rounded-md border border-gray-300 bg-gray-100 p-3 focus:border-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-200">

                        <!-- Password Field -->
                        <input type="password" name="password" placeholder="Password" required
                               class="mt-4 w-full rounded-md border border-gray-300 bg-gray-100 p-3 focus:border-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-200">

                        <!-- CAPTCHA Section -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verifikasi CAPTCHA</label>

                            <!-- CAPTCHA Display -->
                            <div class="flex items-center gap-3">
                                <div id="captcha-display" class="captcha-container px-4 py-3 rounded-md text-xl text-center min-w-[120px]">
                                    <!-- CAPTCHA will be generated here -->
                                </div>

                                <!-- Refresh Button -->
                                <button type="button" onclick="generateCaptcha()"
                                        class="refresh-captcha p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300"
                                        title="Refresh CAPTCHA">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <polyline points="1 20 1 14 7 14"></polyline>
                                        <path d="m3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- CAPTCHA Input -->
                            <input type="text" id="captcha-input" name="captcha" placeholder="Masukkan hasil perhitungan" required
                                   class="mt-2 w-full rounded-md border border-gray-300 bg-gray-100 p-3 focus:border-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-200"
                                   autocomplete="off">

                            <!-- Error Message -->
                            <div id="captcha-error" class="mt-2 text-red-600 text-sm hidden">
                                CAPTCHA tidak valid. Silakan coba lagi.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="mt-6 w-full rounded-md bg-black py-3 text-white font-bold transition-colors hover:bg-yellow-500">
                            SIGN IN
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Panel -->
            <div style="background-image: url('images/logo-unmul.png');"
                class="relative hidden w-3/3 bg-contain bg-center bg-no-repeat text-center lg:flex lg:rounded-bl-[100px] lg:rounded-br-2xl lg:rounded-tr-2xl">

                <div class="absolute inset-0 rounded-br-2xl rounded-tr-2xl bg-yellow-400/90 lg:rounded-bl-[100px]"></div>

                <div class="relative z-10 flex w-full items-center justify-center p-8">
                    <h2 class="text-5xl font-bold text-black [-webkit-text-stroke:1px_white]">
                        Selamat Datang di Website Kepegawaian Universitas Mulawarman
                    </h2>
                </div>
            </div>

        </div>
    </main>

    <script>
        let captchaAnswer = 0;

        function generateCaptcha() {
            // Generate random numbers for simple math captcha
            const num1 = Math.floor(Math.random() * 20) + 1;
            const num2 = Math.floor(Math.random() * 20) + 1;
            const operators = ['+', '-', '*'];
            const operator = operators[Math.floor(Math.random() * operators.length)];

            let question, answer;

            switch(operator) {
                case '+':
                    question = `${num1} + ${num2}`;
                    answer = num1 + num2;
                    break;
                case '-':
                    // Ensure positive result
                    const larger = Math.max(num1, num2);
                    const smaller = Math.min(num1, num2);
                    question = `${larger} - ${smaller}`;
                    answer = larger - smaller;
                    break;
                case '*':
                    const smallNum1 = Math.floor(Math.random() * 10) + 1;
                    const smallNum2 = Math.floor(Math.random() * 10) + 1;
                    question = `${smallNum1} × ${smallNum2}`;
                    answer = smallNum1 * smallNum2;
                    break;
            }

            captchaAnswer = answer;
            document.getElementById('captcha-display').textContent = question + ' = ?';
            document.getElementById('captcha-input').value = '';
            document.getElementById('captcha-error').classList.add('hidden');
        }

        function validateCaptcha(event) {
            const userAnswer = parseInt(document.getElementById('captcha-input').value);
            const errorDiv = document.getElementById('captcha-error');

            if (userAnswer !== captchaAnswer) {
                event.preventDefault();
                errorDiv.classList.remove('hidden');
                generateCaptcha(); // Generate new captcha
                return false;
            }

            errorDiv.classList.add('hidden');
            return true;
        }

        // Generate initial captcha when page loads
        document.addEventListener('DOMContentLoaded', function() {
            generateCaptcha();
        });

        // Clear error message when user starts typing
        document.getElementById('captcha-input').addEventListener('input', function() {
            document.getElementById('captcha-error').classList.add('hidden');
        });
    </script>

</body>
</html>
