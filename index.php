<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Love Diaries - Book a Loyalty Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <style>
        @keyframes pulseGlow {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.4; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.7; }
        }
        .animated-bg-glow {
            position: fixed;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.35) 0%, rgba(15, 23, 42, 0) 70%);
            top: 50%;
            left: 50%;
            z-index: 0;
            pointer-events: none;
            animation: pulseGlow 7s infinite ease-in-out;
        }
        /* Light mode overrides */
        body.light-mode {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }
        body.light-mode .card-bg {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        body.light-mode .input-bg {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        body.light-mode .footer-btn {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: #e2e8f0 !important;
            color: #475569 !important;
        }
        body.light-mode .animated-bg-glow {
            background: radial-gradient(circle, rgba(225, 29, 72, 0.15) 0%, rgba(248, 250, 252, 0) 70%);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col items-center justify-center p-4 relative overflow-x-hidden transition-colors duration-300">

    <!-- Background Glow Animation -->
    <div class="animated-bg-glow"></div>

    <!-- Theme Toggle Button -->
    <button onclick="toggleTheme()" class="absolute top-4 right-4 z-20 text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-slate-300 hover:text-rose-500 transition duration-200 shadow-md">
        <span id="themeIcon">☀️</span> <span id="themeText">Light Mode</span>
    </button>

    <div class="max-w-md w-full card-bg bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-2xl my-auto relative z-10 transition-colors duration-300">
        
        <!-- Region / Currency Switcher Tabs -->
        <div class="grid grid-cols-2 gap-2 bg-slate-950 p-1 rounded-xl border border-slate-800 mb-6">
            <button type="button" id="localTab" onclick="setRegion('local')" class="py-2 text-xs font-bold rounded-lg bg-rose-600 text-white transition">
                🇰🇪 Local (Ksh 100)
            </button>
            <button type="button" id="intlTab" onclick="setRegion('intl')" class="py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition">
                🌍 International (Ksh 150)
            </button>
        </div>

        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-rose-500">LOVE DIARIES</h1>
            <p id="subText" class="text-sm text-slate-400 mt-1">Book your loyalty test slot (Ksh 100 fee)</p>
        </div>

        <form id="bookingForm" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Your Full Name</label>
                <input type="text" id="client_name" required class="w-full input-bg bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 transition-colors duration-300">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Your Phone Number</label>
                <input type="text" id="client_phone" placeholder="07XXXXXXXX or international format" required class="w-full input-bg bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 transition-colors duration-300">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Partner's Name (Target)</label>
                <input type="text" id="target_name" required class="w-full input-bg bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 transition-colors duration-300">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Partner's Phone Number</label>
                <input type="text" id="target_phone" required class="w-full input-bg bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 transition-colors duration-300">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Optional additional details not a must</label>
                <textarea id="notes" rows="2" class="w-full input-bg bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-rose-500 transition-colors duration-300"></textarea>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-3 rounded-xl transition duration-200 shadow-lg shadow-rose-900/40">
                Pay Ksh 100 & Get Booking Number
            </button>
        </form>
    </div>

    <!-- Footer Links -->
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-3 relative z-10">
        <a href="https://wa.me/254790182919?text=Hello%20Love%20Diaries,%20I%20have%20an%20inquiry%20regarding%20booking%20a%20test." target="_blank" class="footer-btn inline-flex items-center gap-2 text-xs text-slate-400 bg-slate-900/90 backdrop-blur-sm border border-slate-800 hover:border-emerald-500/50 px-4 py-2 rounded-full transition duration-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            WhatsApp: <span class="text-slate-200 font-semibold">254790182919</span>
        </a>

        <a href="admin.php" class="footer-btn text-xs text-slate-500 bg-slate-900/90 backdrop-blur-sm border border-slate-800 hover:text-rose-400 px-4 py-2 rounded-full transition duration-200">
            🔒 Staff Portal
        </a>
    </div>

    <script>
        let currentRegion = 'local';

        function setRegion(region) {
            currentRegion = region;
            const localTab = document.getElementById('localTab');
            const intlTab = document.getElementById('intlTab');
            const subText = document.getElementById('subText');
            const submitBtn = document.getElementById('submitBtn');

            if (region === 'local') {
                localTab.className = "py-2 text-xs font-bold rounded-lg bg-rose-600 text-white transition";
                intlTab.className = "py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition";
                subText.textContent = "Book your loyalty test slot (Ksh 100 fee)";
                submitBtn.textContent = "Pay Ksh 100 & Get Booking Number";
            } else {
                intlTab.className = "py-2 text-xs font-bold rounded-lg bg-rose-600 text-white transition";
                localTab.className = "py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition";
                subText.textContent = "Book your loyalty test slot (Ksh 150 international fee)";
                submitBtn.textContent = "Pay Ksh 150 & Get Booking Number";
            }
        }

        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            
            body.classList.toggle('light-mode');
            
            if (body.classList.contains('light-mode')) {
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Dark Mode';
                localStorage.setItem('theme', 'light');
            } else {
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Light Mode';
                localStorage.setItem('theme', 'dark');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('theme') === 'light') {
                document.body.classList.add('light-mode');
                document.getElementById('themeIcon').textContent = '🌙';
                document.getElementById('themeText').textContent = 'Dark Mode';
            }
        });

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const clientName = document.getElementById('client_name').value;
            const clientPhone = document.getElementById('client_phone').value;
            const dummyEmail = clientPhone.replace(/[^0-9]/g, '') + "@lovediarries.ke"; 

            // Both options process securely in KES to prevent gateway currency mismatches
            let amount = currentRegion === 'local' ? 10000 : 15000; // 10000 subunits = Ksh 100, 15000 subunits = Ksh 150
            let currency = 'KES'; 
            let channels = currentRegion === 'local' ? ['mobile_money', 'card'] : ['card'];

            let handler = PaystackPop.setup({
                key: 'pk_live_952e2367041f2dcf97bc43be4f29de307bf1abad',
                email: dummyEmail,
                amount: amount, 
                currency: currency,
                channels: channels,
                metadata: {
                    custom_fields: [
                        { display_name: "Client Name", variable_name: "client_name", value: clientName },
                        { display_name: "Client Phone", variable_name: "client_phone", value: clientPhone },
                        { display_name: "Target Name", variable_name: "target_name", value: document.getElementById('target_name').value },
                        { display_name: "Target Phone", variable_name: "target_phone", value: document.getElementById('target_phone').value },
                        { display_name: "Notes", variable_name: "notes", value: document.getElementById('notes').value }
                    ]
                },
                callback: function(response) {
                    window.location.href = `success.php?reference=${response.reference}`;
                },
                onClose: function() {
                    alert('Payment window closed.');
                }
            });
            handler.openIframe();
        });
    </script>
</body>
</html>
