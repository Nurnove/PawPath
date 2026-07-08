<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// =========================
// AUTH CHECK
// =========================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$shelter = $conn->query("
    SELECT id FROM shelters WHERE user_id = $user_id
")->fetch_assoc();

$shelter_id = $shelter['id'];

$pets = $conn->query("
    SELECT * 
    FROM pets 
    WHERE shelter_id = $shelter_id
    AND owner_type = 'shelter'
    AND is_deleted = 0
");
?>

<div class="p-6 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#2D6A4F] flex items-center gap-2">
            <!-- Paw icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.418 0-8-2.91-8-6.5 0-1.37.537-2.638 1.44-3.664M12 21c4.418 0 8-2.91 8-6.5 0-1.37-.537-2.638-1.44-3.664M12 21v0M7 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm10 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5 13.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm14 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
            </svg>
            Manage Pets
        </h1>

        <a href="add-pet.php"
           class="flex items-center gap-1 bg-[#2D6A4F] text-white px-4 py-2 rounded-lg">
            <!-- Plus icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Pet
        </a>
    </div>

    <!-- PET LIST -->
    <?php if ($pets && $pets->num_rows > 0): ?>

        <div class="grid md:grid-cols-3 gap-6">

            <?php while ($p = $pets->fetch_assoc()): ?>

                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 group">

                    <!-- IMAGE -->
                    <div class="relative h-44 w-full overflow-hidden bg-gray-100">
                        <img src="<?= !empty($p['image']) ? '../assets/images/'.$p['image'] : '../assets/images/default.png' ?>"
                             class="w-full h-full object-cover object-center transition duration-500 group-hover:scale-105"
                             onerror="this.src='../assets/images/default.png'">

                        <!-- STATUS BADGE (TOP RIGHT) -->
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                <?= $p['status'] == 'available' ? 'bg-green-100 text-green-700' : '' ?>
                                <?= $p['status'] == 'adopted'   ? 'bg-red-100 text-red-600'     : '' ?>
                                <?= $p['status'] == 'fostered'  ? 'bg-blue-100 text-blue-600'   : '' ?>
                            ">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="p-5">

                        <!-- NAME -->
                        <h2 class="text-xl font-bold text-gray-800 group-hover:text-[#2D6A4F] transition">
                            <?= htmlspecialchars($p['name']) ?>
                        </h2>

                        <!-- CATEGORY + STATUS BADGES -->
                        <div class="flex gap-2 mt-2 flex-wrap">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                <?= $p['category'] == 'adoption' ? 'bg-purple-100 text-purple-700' : '' ?>
                                <?= $p['category'] == 'foster'   ? 'bg-yellow-100 text-yellow-700' : '' ?>
                            ">
                                <?= ucfirst($p['category']) ?>
                            </span>

                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                <?= $p['status'] == 'available' ? 'bg-green-100 text-green-700' : '' ?>
                                <?= $p['status'] == 'adopted'   ? 'bg-red-100 text-red-600'     : '' ?>
                                <?= $p['status'] == 'fostered'  ? 'bg-blue-100 text-blue-600'   : '' ?>
                            ">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </div>

                        <!-- DETAILS -->
                        <div class="mt-3 text-sm text-gray-500 space-y-1">

                            <!-- Type -->
                            <p class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.418 0-8-2.91-8-6.5 0-1.37.537-2.638 1.44-3.664M12 21c4.418 0 8-2.91 8-6.5 0-1.37-.537-2.638-1.44-3.664M7 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm10 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5 13.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm14 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                                </svg>
                                Type: <span class="font-medium text-gray-700"><?= htmlspecialchars($p['type']) ?></span>
                            </p>

                            <!-- Age -->
                            <p class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                </svg>
                                Age: <span class="font-medium text-gray-700"><?= htmlspecialchars($p['age']) ?></span>
                            </p>

                            <!-- Activity Level -->
                            <?php if (!empty($p['activity_level'])): ?>
                            <p class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Activity: <span class="font-medium text-gray-700"><?= $p['activity_level'] ?></span>
                            </p>
                            <?php endif; ?>

                        </div>

                        <!-- FOSTER DURATION -->
                        <?php if ($p['category'] == 'foster' && !empty($p['foster_duration'])): ?>
                        <div class="mt-2 text-sm text-gray-500 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                            </svg>
                            Duration: <span class="font-medium text-gray-700"><?= htmlspecialchars($p['foster_duration']) ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- DIVIDER -->
                        <div class="my-4 border-t"></div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex justify-between items-center">

                            <a href="edit-pet.php?id=<?= $p['id'] ?>"
                               class="flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition">
                                <!-- Pencil icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 1 1 2.828 2.828L11.828 15.828a2 2 0 0 1-1.414.586H8v-2.414a2 2 0 0 1 .586-1.414z"/>
                                </svg>
                                Edit
                            </a>

                            <a href="delete-pet.php?id=<?= $p['id'] ?>"
                               onclick="return confirm('Are you sure you want to delete this pet?')"
                               class="flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                                <!-- Trash icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <!-- EMPTY STATE -->
        <div class="text-center text-gray-500 mt-20">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.418 0-8-2.91-8-6.5 0-1.37.537-2.638 1.44-3.664M12 21c4.418 0 8-2.91 8-6.5 0-1.37-.537-2.638-1.44-3.664M7 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm10 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5 13.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm14 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
            </svg>
            <p class="text-xl">No pets found</p>
            <p class="mt-2">Start by adding your first pet</p>

            <a href="add-pet.php"
               class="inline-flex items-center gap-1.5 mt-4 bg-[#2D6A4F] text-white px-5 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Pet
            </a>
        </div>

    <?php endif; ?>

</div>