<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            $user_id = (int)$user['id'];

            // ADMIN
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit();
            }

            // SHELTER
            if ($user['role'] == 'shelter') {

                $checkShelter = $conn->query("
                    SELECT id 
                    FROM shelters
                    WHERE user_id = $user_id
                ");

                if ($checkShelter->num_rows == 0) {
                    header("Location: ../shelter/complete-profile.php");
                } else {
                    header("Location: ../shelter/dashboard.php");
                }
                exit();
            }

            // ELIGIBILITY CHECK
            $attempt = $conn->query("
                SELECT is_eligible 
                FROM eligibility_attempts 
                WHERE user_id = $user_id 
                ORDER BY id DESC 
                LIMIT 1
            ");

            $attempt = $attempt ? $attempt->fetch_assoc() : null;

            if (!$attempt) {
                header("Location: ../quiz/eligibility.php");
                exit();
            }

            if ((int)$attempt['is_eligible'] === 0) {
                header("Location: ../user/not-eligible.php");
                exit();
            }

            header("Location: ../user/dashboard.php");
            exit();

        } else {
            $error = "Wrong password!";
        }

    } else {
        $error = "Email not found!";
    }
}
?>

<nav class="w-full bg-[#D9D9D9] px-6 py-4 flex justify-between items-center shadow-sm">
    <a href="../index.php" class="text-2xl font-serif flex items-baseline">
        <span class="text-[#6B8E23] italic">Paw</span>
        <span class="text-[#2B4C7E] ml-1">Path</span>
    </a>
    <div class="flex gap-6 text-sm font-medium">
        <a href="../index.php" class="hover:text-[#2D6A4F]">Home</a>
    </div>
</nav>

<div class="flex flex-col md:flex-row min-h-screen">

    <!-- Left Side Image -->
    <div class="hidden md:block md:w-1/2 relative overflow-hidden">

        <img src="../assets/images/adopt.jpg"
             class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30"></div>

        <div class="absolute bottom-10 left-10 text-white">
            <h2 class="text-3xl font-bold">
                Welcome Back
            </h2>

            <p class="text-sm">
                Login and continue your pet adoption journey.
            </p>
        </div>

    </div>

    <!-- Right Side Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-6">

        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">

            <h1 class="text-3xl font-bold mb-2">
                Login
            </h1>

            <p class="text-gray-500 mb-6 text-sm">
                Access your PawPath account
            </p>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded-xl mb-4 text-center text-sm">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded-xl mb-4 text-center text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]"
                    required
                >

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]"
                    required
                >

                <button
                    type="submit"
                    class="w-full bg-[#2D6A4F] text-white py-4 rounded-xl font-semibold hover:bg-[#40916c] transition"
                >
                    Login
                </button>

            </form>

            <p class="text-sm text-center mt-6">
                Don't have an account?
                <a href="register.php"
                   class="text-[#2D6A4F] font-semibold">
                    Create Account
                </a>
            </p>

        </div>

    </div>

</div>