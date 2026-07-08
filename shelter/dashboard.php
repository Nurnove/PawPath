<?php
session_start();

include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] != 'shelter') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$shelter = $conn->query("
    SELECT *
    FROM shelters
    WHERE user_id = $user_id
")->fetch_assoc();

if (!$shelter) {
    header("Location: complete-profile.php");
    exit();
}

$shelter_id = $shelter['id'];

$total_adoption = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND category = 'adoption'
    AND is_deleted = 0
")->fetch_assoc()['total'];

$total_foster = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND category = 'foster'
    AND is_deleted = 0
")->fetch_assoc()['total'];

$available = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND status = 'available'
    AND is_deleted = 0
")->fetch_assoc()['total'];

$adopted = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND status = 'adopted'
    AND is_deleted = 0
")->fetch_assoc()['total'];

$fostered = $conn->query("
    SELECT COUNT(*) as total
    FROM pets
    WHERE shelter_id = $shelter_id
    AND status = 'fostered'
    AND is_deleted = 0
")->fetch_assoc()['total'];

$total_applications = $conn->query("
    SELECT COUNT(*) as total
    FROM adoption_applications aa
    JOIN pets p ON aa.pet_id = p.id
    WHERE p.shelter_id = $shelter_id
")->fetch_assoc()['total'];

$recent = $conn->query("
    SELECT
        aa.id,
        aa.status,
        aa.is_withdrawn,
        aa.created_at,
        u.name AS user_name,
        p.name AS pet_name
    FROM adoption_applications aa
    JOIN users u ON aa.user_id = u.id
    JOIN pets p ON aa.pet_id = p.id
    WHERE p.shelter_id = $shelter_id
    ORDER BY aa.created_at DESC
    LIMIT 5
");
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="bg-gray-50 min-h-screen px-6 md:px-12 py-10">

    <!-- HEADER -->
    <div class="mb-10">
        <div class="flex items-center gap-3">
            <div class="bg-[#2D6A4F] text-white p-3 rounded-xl">
                <i class="fa-solid fa-house-chimney text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-[#2D6A4F]">Shelter Dashboard</h1>
                <p class="text-gray-500 mt-0.5">Welcome back, <?= htmlspecialchars($user_name) ?></p>
            </div>
        </div>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5 mb-12">

        <div class="bg-white p-5 rounded-2xl shadow">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-2">
                <i class="fa-solid fa-paw text-[#2D6A4F]"></i>
                Adoption Pets
            </div>
            <p class="text-3xl font-bold text-[#2D6A4F]"><?= $total_adoption ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-2">
                <i class="fa-solid fa-heart text-[#2D6A4F]"></i>
                Foster Pets
            </div>
            <p class="text-3xl font-bold text-[#2D6A4F]"><?= $total_foster ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-2">
                <i class="fa-solid fa-circle-check text-[#2D6A4F]"></i>
                Available
            </div>
            <p class="text-3xl font-bold text-[#2D6A4F]"><?= $available ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-2">
                <i class="fa-solid fa-house-user text-[#2D6A4F]"></i>
                Adopted
            </div>
            <p class="text-3xl font-bold text-[#2D6A4F]"><?= $adopted ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-2">
                <i class="fa-solid fa-hand-holding-heart text-[#2D6A4F]"></i>
                Fostered
            </div>
            <p class="text-3xl font-bold text-[#2D6A4F]"><?= $fostered ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-2">
                <i class="fa-solid fa-file-lines text-[#2D6A4F]"></i>
                Applications
            </div>
            <p class="text-3xl font-bold text-[#2D6A4F]"><?= $total_applications ?></p>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div class="flex flex-wrap gap-4 mb-12">

        <a href="add-pet.php"
           class="flex items-center gap-2 bg-[#2D6A4F] text-white px-6 py-3 rounded-xl shadow hover:bg-[#40916c] transition">
            <i class="fa-solid fa-plus"></i>
            Add Pet
        </a>

        <a href="manage-pets.php"
           class="flex items-center gap-2 bg-white border px-6 py-3 rounded-xl shadow hover:bg-gray-100 transition">
            <i class="fa-solid fa-list text-[#2D6A4F]"></i>
            Manage Pets
        </a>

        <a href="pet-applications.php"
           class="flex items-center gap-2 bg-white border px-6 py-3 rounded-xl shadow hover:bg-gray-100 transition">
            <i class="fa-solid fa-clipboard-list text-[#2D6A4F]"></i>
            Applications
        </a>

    </div>

    <!-- RECENT APPLICATIONS -->
    <div class="bg-white rounded-2xl shadow p-6">

        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-[#2D6A4F] text-lg"></i>
                <h2 class="text-2xl font-bold">Recent Applications</h2>
            </div>
            <a href="pet-applications.php"
               class="flex items-center gap-1 text-[#2D6A4F] font-semibold hover:underline">
                View All
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        </div>

        <?php if ($recent->num_rows > 0): ?>

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 text-gray-500 font-medium">Applicant</th>
                            <th class="text-left py-3 text-gray-500 font-medium">Pet</th>
                            <th class="text-left py-3 text-gray-500 font-medium">Status</th>
                            <th class="text-left py-3 text-gray-500 font-medium">Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $recent->fetch_assoc()): ?>

                            <?php
                            $displayStatus = ($row['is_withdrawn'] == 1) ? 'withdrawn' : $row['status'];
                            $statusClass = match($displayStatus) {
                                'approved'  => 'bg-green-100 text-green-700',
                                'rejected'  => 'bg-red-100 text-red-700',
                                'withdrawn' => 'bg-gray-200 text-gray-600',
                                default     => 'bg-yellow-100 text-yellow-700'
                            };
                            $statusIcon = match($displayStatus) {
                                'approved'  => 'fa-circle-check',
                                'rejected'  => 'fa-circle-xmark',
                                'withdrawn' => 'fa-ban',
                                default     => 'fa-clock'
                            };
                            ?>

                            <tr class="border-b hover:bg-gray-50">

                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-user text-xs"></i>
                                        </div>
                                        <?= htmlspecialchars($row['user_name']) ?>
                                    </div>
                                </td>

                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-paw text-[#2D6A4F] text-xs"></i>
                                        <?= htmlspecialchars($row['pet_name']) ?>
                                    </div>
                                </td>

                                <td class="py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                                        <i class="fa-solid <?= $statusIcon ?> text-xs"></i>
                                        <?= ucfirst($displayStatus) ?>
                                    </span>
                                </td>

                                <td class="py-4 text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-calendar text-gray-400"></i>
                                        <?= date("d M Y", strtotime($row['created_at'])) ?>
                                    </div>
                                </td>

                            </tr>

                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        <?php else: ?>
            <div class="text-center py-10 text-gray-400">
                <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                <p>No applications found.</p>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php include '../includes/footer.php'; ?>