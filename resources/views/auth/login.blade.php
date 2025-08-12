<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kepegawaian UNMUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .captcha-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 2px solid rgba(255,255,255,0.2);
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            color: white;
            backdrop-filter: blur(10px);
            animation: captcha-glow 3s ease-in-out infinite alternate;
        }

        @keyframes captcha-glow {
            0% { box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4); }
            100% { box-shadow: 0 8px 30px rgba(118, 75, 162, 0.6); }
        }

        .refresh-captcha {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .refresh-captcha:hover {
            transform: rotate(180deg) scale(1.1);
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .slide-in-left {
            animation: slideInLeft 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .slide-in-right {
            animation: slideInRight 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes slideInLeft {
            0% {
                transform: translateX(-100px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(100px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(209, 213, 219, 0.3);
        }

        .input-focus-effect:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }

        .button-hover-effect {
            background: linear-gradient(135deg, #1f2937, #374151);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .button-hover-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .button-hover-effect:hover::before {
            left: 100%;
        }

        .button-hover-effect:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .error-shake {
            animation: shake 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .pulse-border {
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0% { border-color: #d1d5db; }
            50% { border-color: #667eea; }
            100% { border-color: #d1d5db; }
        }

        .background-pattern {
            background-image:
                url('images/bg-unmul.jpg'),
                radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
            background-size: cover, 100% 100%, 100% 100%;
            background-position: center, center, center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body style="background-image: url('images/bg-unmul.jpg');" class="bg-cover bg-center bg-no-repeat min-h-screen background-pattern">

    <main class="flex min-h-screen items-center justify-center p-4">
        <div class="relative flex w-full max-w-6xl overflow-hidden rounded-3xl shadow-2xl">

            <!-- Form Section -->
            <div class="w-full glass-effect p-8 sm:p-12 lg:w-1/2 slide-in-left">
                <div class="text-center mb-8">
                    <div class="floating-animation">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold gradient-text mb-2">Selamat Datang</h1>
                    <p class="text-gray-600">Masuk ke sistem kepegawaian UNMUL</p>
                </div>

                <div id="error-container" class="mb-4 hidden">
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg" role="alert">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <p id="error-message">Terjadi kesalahan</p>
                        </div>
                    </div>
                </div>

                <form onsubmit="return validateCaptcha(event)" class="space-y-6">
                    <div class="relative">
                        <input type="text"
                               name="nip"
                               placeholder=" "
                               required
                               class="peer w-full rounded-xl border-2 border-gray-200 bg-white/80 backdrop-blur px-4 pt-6 pb-2 text-gray-900 focus:border-purple-500 focus:outline-none transition-all duration-300 input-focus-effect"
                               autocomplete="username">
                        <label class="absolute left-4 top-2 text-xs font-medium text-gray-500 transition-all duration-300 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-purple-600">
                            Nomor Induk Pegawai (NIP)
                        </label>
                    </div>

                    <div class="relative">
                        <input type="password"
                               name="password"
                               placeholder=" "
                               required
                               class="peer w-full rounded-xl border-2 border-gray-200 bg-white/80 backdrop-blur px-4 pt-6 pb-2 text-gray-900 focus:border-purple-500 focus:outline-none transition-all duration-300 input-focus-effect"
                               autocomplete="current-password">
                        <label class="absolute left-4 top-2 text-xs font-medium text-gray-500 transition-all duration-300 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-purple-600">
                            Kata Sandi
                        </label>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-700">Verifikasi CAPTCHA</label>
                        <div class="flex items-center gap-4">
                            <div id="captcha-display"
                                 class="captcha-container px-6 py-4 rounded-xl text-xl text-center min-w-[140px] font-mono">
                            </div>
                            <button type="button"
                                    onclick="generateCaptcha()"
                                    class="refresh-captcha p-3 bg-white/80 backdrop-blur hover:bg-gradient-to-br hover:from-blue-500 hover:to-purple-600 rounded-xl border-2 border-gray-200 hover:border-transparent group"
                                    title="Refresh CAPTCHA">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="group-hover:text-white">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <polyline points="1 20 1 14 7 14"></polyline>
                                    <path d="m3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="text"
                                   id="captcha-input"
                                   name="captcha"
                                   placeholder=" "
                                   required
                                   class="peer w-full rounded-xl border-2 border-gray-200 bg-white/80 backdrop-blur px-4 pt-6 pb-2 text-gray-900 focus:border-purple-500 focus:outline-none transition-all duration-300 input-focus-effect"
                                   autocomplete="off">
                            <label class="absolute left-4 top-2 text-xs font-medium text-gray-500 transition-all duration-300 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-purple-600">
                                Masukkan hasil perhitungan
                            </label>
                        </div>
                        <div id="captcha-error" class="text-red-500 text-sm font-medium hidden flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            CAPTCHA tidak valid. Silakan coba lagi.
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl py-4 text-white font-bold text-lg button-hover-effect relative overflow-hidden">
                        <span class="relative z-10">MASUK SISTEM</span>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        © 2024 Universitas Mulawarman. All rights reserved.
                    </p>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="relative hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-800 slide-in-right">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-yellow-300 rounded-full blur-3xl animate-pulse delay-1000"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center p-12 text-center text-white">
                    <div class="floating-animation mb-8">
                        <div class="w-32 h-32 mx-auto bg-white/20 backdrop-blur rounded-full flex items-center justify-center border border-white/30">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-5xl font-bold mb-6 leading-tight">
                        Sistem Informasi<br>
                        <span class="text-yellow-300">Kepegawaian</span>
                    </h2>

                    <p class="text-xl opacity-90 mb-8 max-w-md leading-relaxed">
                        Universitas Mulawarman - Menuju Excellence dengan Kearifan Lokal
                    </p>

                    <div class="flex space-x-4">
                        <div class="w-3 h-3 bg-white rounded-full animate-bounce"></div>
                        <div class="w-3 h-3 bg-yellow-300 rounded-full animate-bounce delay-100"></div>
                        <div class="w-3 h-3 bg-white rounded-full animate-bounce delay-200"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let captchaAnswer = 0;

        function generateCaptcha() {
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
            const captchaInput = document.getElementById('captcha-input');
            const captchaError = document.getElementById('captcha-error');

            if (userAnswer !== captchaAnswer) {
                event.preventDefault();

                // Add shake animation to captcha input
                captchaInput.classList.add('error-shake', 'border-red-500');
                captchaError.classList.remove('hidden');

                // Remove shake animation after it completes
                setTimeout(() => {
                    captchaInput.classList.remove('error-shake', 'border-red-500');
                }, 600);

                generateCaptcha();
                return false;
            }
            return true;
        }

        // Initialize captcha when page loads
        document.addEventListener('DOMContentLoaded', function() {
            generateCaptcha();

            // Add input event listener to hide error when user starts typing
            document.getElementById('captcha-input').addEventListener('input', function() {
                document.getElementById('captcha-error').classList.add('hidden');
                this.classList.remove('border-red-500');
            });

            // Add focus effects to all inputs
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('pulse-border');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('pulse-border');
                });
            });
        });
    </script>
</body>
</html>
