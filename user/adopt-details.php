<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// =========================
// LOGIN CHECK
// =========================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// =========================
// ELIGIBILITY CHECK
// =========================
$attempt = $conn->query("
    SELECT is_eligible FROM eligibility_attempts
    WHERE user_id = $user_id
    ORDER BY id DESC LIMIT 1
")->fetch_assoc();
$is_eligible = $attempt ? (int)$attempt['is_eligible'] : 0;

// =========================
// ALREADY APPLIED CHECK
// =========================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// =========================
// FETCH PET
// =========================
$pet = $conn->query("
    SELECT * FROM pets
    WHERE id = $id
    AND category = 'adoption'
    AND is_deleted = 0
    LIMIT 1
")->fetch_assoc();

// PET NOT FOUND
if (!$pet) {
    die("Pet not found!");
}
?>

<section class="py-10 px-4 md:px-10 lg:px-16">

    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col lg:flex-row gap-8 items-stretch">

            <!-- IMAGE -->
            <div
                class="bg-[#E1CBB9] p-6 rounded-[35px] flex-1 shadow-2xl border border-[#d1b8a5] flex">

                <div
                    class="relative flex-1 overflow-hidden rounded-[25px] border-2 border-[#2d4d6a] group">

                    <img src="../assets/images/<?= htmlspecialchars($pet['image']) ?>"
                         alt="<?= htmlspecialchars($pet['name']) ?>"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                </div>

            </div>

            <!-- DETAILS -->
            <div
                class="bg-[#E1CBB9] p-8 rounded-[35px] flex-1 shadow-xl flex flex-col">

                <!-- HEADER -->
                <div class="flex justify-between items-start flex-wrap gap-4">

                    <div>

                        <h1 class="text-4xl md:text-5xl font-bold">
                            <?= htmlspecialchars($pet['name']) ?>
                        </h1>

                        <p class="text-gray-600 mt-2">
                            Friendly & Lovely <?= htmlspecialchars($pet['type']) ?>
                        </p>

                    </div>

                    <span
                        class="
                        px-4 py-2 rounded-xl font-semibold

                        <?= $pet['status'] == 'available'
                            ? 'bg-green-100 text-[#355E3B]'
                            : '' ?>

                        <?= $pet['status'] == 'adopted'
                            ? 'bg-red-200 text-red-700'
                            : '' ?>
                        ">

                        <?= ucfirst($pet['status']) ?>

                    </span>

                </div>

                <!-- GRID INFO -->
                <div class="grid grid-cols-2 gap-4 mt-8">

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Age</p>
                        <h3 class="font-semibold"><?= htmlspecialchars($pet['age']) ?></h3>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Gender</p>
                        <h3 class="font-semibold"><?= htmlspecialchars($pet['gender']) ?></h3>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Pet Type</p>
                        <h3 class="font-semibold"><?= htmlspecialchars($pet['type']) ?></h3>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-500 text-sm">Activity</p>
                        <h3 class="font-semibold"><?= htmlspecialchars($pet['activity_level']) ?></h3>
                    </div>

                </div>

                <!-- HEALTH -->
                <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">

                    <p class="text-gray-500 text-sm">Health Status</p>

                    <h3 class="font-semibold break-words">
                        <?= htmlspecialchars($pet['health_status']) ?>
                    </h3>

                </div>

                <!-- DESCRIPTION -->
                <div class="mt-6 flex-grow">

                    <h3 class="text-2xl font-bold mb-3">About <?= htmlspecialchars($pet['name']) ?></h3>

                    <div class="bg-white p-4 rounded-xl shadow-sm">

                        <p class="leading-relaxed text-gray-700 break-words">
                            <?= nl2br(htmlspecialchars($pet['description'])) ?>
                        </p>

                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-8">

                    <?php
                    // Check already applied
                    $already = $conn->query("
                        SELECT id FROM adoption_applications
                        WHERE user_id = $user_id AND pet_id = {$pet['id']} AND is_withdrawn = 0
                        LIMIT 1
                    ")->fetch_assoc();
                    ?>

                    <?php if ($already): ?>

                        <button disabled
                                class="w-full bg-gray-400 text-white py-4 rounded-xl cursor-not-allowed">
                            Already Applied
                        </button>

                    <?php elseif (!$is_eligible): ?>

                        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center">
                            <p class="text-red-700 font-semibold mb-3">
                                You are not eligible to adopt pets.
                            </p>
                            <p class="text-gray-600 text-sm mb-4">
                                Please retake the eligibility quiz to qualify for adoption.
                            </p>
                            <a href="../quiz/eligibility.php"
                               class="inline-block bg-[#2D6A4F] text-white font-bold px-8 py-3 rounded-xl hover:bg-[#40916c] transition">
                                Retake Quiz
                            </a>
                        </div>

                    <?php elseif ($pet['status'] == 'available'): ?>

                        <a href="apply-adoption.php?id=<?= $pet['id'] ?>"
                           class="block text-center w-full bg-[#6B704C] hover:bg-[#59603f] text-white py-4 rounded-xl transition">
                            Apply to Adopt
                        </a>

                    <?php else: ?>

                        <button disabled
                                class="w-full bg-gray-400 text-white py-4 rounded-xl cursor-not-allowed">
                            Not Available
                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>