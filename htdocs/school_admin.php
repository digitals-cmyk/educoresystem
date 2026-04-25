<?php
// school_admin.php
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['SCHOOL_ADMIN', 'TEACHER'])) {
    header("location: index.php");
    exit;
}

$school_id = $_SESSION["school_id"];

// Fetch counts
$sql = "SELECT COUNT(*) as count FROM students WHERE school_id = :school_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":school_id", $school_id);
$stmt->execute();
$total_students = $stmt->fetchColumn() ?: 0;

$sql = "SELECT COUNT(*) as count FROM exams WHERE school_id = :school_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":school_id", $school_id);
$stmt->execute();
$total_exams = $stmt->fetchColumn() ?: 0;

$sql = "SELECT * FROM schools WHERE id = :school_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":school_id", $school_id);
$stmt->execute();
$school = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Dashboard - EduCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex">
    
    <!-- Sidebar -->
    <aside class="w-20 lg:w-64 bg-slate-900 flex flex-col p-4 lg:p-8 text-white min-h-screen shrink-0">
        <div class="font-bold text-2xl hidden lg:flex mb-10"><div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center font-bold text-xl uppercase leading-none">E</div></div>
        <div class="text-[10px] uppercase tracking-widest font-bold opacity-40 mb-4 mt-2 hidden lg:block">Navigation</div>
        <nav class="flex-1 flex flex-col gap-2">
            <a href="school_admin.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider bg-indigo-600 opacity-100">Dashboard</a>
            <a href="school_registry.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider opacity-60 hover:opacity-100 hover:bg-white/10">Learners</a>
            <a href="school_exams.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider opacity-60 hover:opacity-100 hover:bg-white/10">Exams</a>
        </nav>
        <div class="pt-4 mt-auto hidden lg:block">
            <div class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider opacity-60 truncate mb-2"><?php echo htmlspecialchars($_SESSION["name"]); ?></div>
            <a href="logout.php" class="flex items-center w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-rose-400 hover:bg-rose-500/10 rounded-full transition-colors">Sign Out</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col p-6 lg:p-10 overflow-auto">
        <header class="flex flex-col mb-12">
            <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-indigo-600 mb-2"><?php echo htmlspecialchars($school ? $school['name'] : 'Unknown School'); ?></span>
            <h1 class="text-4xl lg:text-7xl font-black tracking-tighter leading-none uppercase text-slate-900">Dashboard</h1>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-indigo-50 rounded-full opacity-50 blur-2xl block pointer-events-none"></div>
                <span class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1">Total Learners</span>
                <span class="text-6xl lg:text-8xl font-black tracking-tighter leading-none text-slate-900 mt-2"><?php echo $total_students; ?></span>
                <div class="mt-8">
                    <a href="school_registry.php" class="text-xs font-bold uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors inline-block">Manage Learners &rarr;</a>
                </div>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-indigo-50 rounded-full opacity-50 blur-2xl block pointer-events-none"></div>
                <span class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1">Active Exams</span>
                <span class="text-6xl lg:text-8xl font-black tracking-tighter leading-none text-slate-900 mt-2"><?php echo $total_exams; ?></span>
                <div class="mt-8">
                    <a href="school_exams.php" class="text-xs font-bold uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors inline-block">Manage Exams &rarr;</a>
                </div>
            </div>
        </section>

        <section class="bg-white flex-1 rounded-3xl shadow-sm border border-slate-100 p-8 overflow-hidden">
            <h2 class="text-xl font-black uppercase tracking-tight mb-6">Quick Overview</h2>
            <p class="text-slate-500 font-medium">Welcome back to the EduCore management system. Use the sidebar to navigate to the learner registry or to manage examinations.</p>
        </section>
    </main>

</body>
</html>
