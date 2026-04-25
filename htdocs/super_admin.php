<?php
// super_admin.php
require_once "config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "SUPER_ADMIN"){
    header("location: index.php");
    exit;
}

// Fetch schools
$sql = "SELECT * FROM schools ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$schools = $stmt->fetchAll();

$active_schools = 0;
$inactive_schools = 0;
foreach($schools as $school) {
    if($school['status'] == 'ACTIVE') $active_schools++;
    else $inactive_schools++;
}
$total_schools = count($schools);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - EduCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex">
    
    <!-- Sidebar -->
    <aside class="w-20 lg:w-64 bg-slate-900 flex flex-col p-4 lg:p-8 text-white min-h-screen">
        <div class="font-bold text-2xl hidden lg:flex mb-10"><div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center font-bold text-xl uppercase leading-none">E</div></div>
        <div class="text-[10px] uppercase tracking-widest font-bold opacity-40 mb-4 mt-2 hidden lg:block">Super Admin</div>
        <nav class="flex-1 flex flex-col gap-2">
            <a href="super_admin.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider bg-indigo-600 opacity-100">Dashboard</a>
            <a href="#" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider opacity-60 hover:opacity-100 hover:bg-white/10">Schools</a>
        </nav>
        <div class="pt-4 mt-auto hidden lg:block">
            <div class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider opacity-60 truncate mb-2"><?php echo htmlspecialchars($_SESSION["name"]); ?></div>
            <a href="logout.php" class="flex items-center w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-rose-400 hover:bg-rose-500/10 rounded-full transition-colors">Sign Out</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col p-6 lg:p-10 overflow-auto">
        <header class="flex flex-col mb-12">
            <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-indigo-600 mb-2">Super Admin Portal</span>
            <h1 class="text-4xl lg:text-7xl font-black tracking-tighter leading-none uppercase text-slate-900">Dashboard</h1>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                <span class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1">Total Schools</span>
                <span class="text-6xl lg:text-8xl font-black tracking-tighter leading-none text-slate-900"><?php echo $total_schools; ?></span>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                <span class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1">Active Schools</span>
                <span class="text-6xl lg:text-8xl font-black tracking-tighter leading-none text-indigo-600"><?php echo $active_schools; ?></span>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                <span class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1">Inactive Schools</span>
                <span class="text-6xl lg:text-8xl font-black tracking-tighter leading-none text-slate-900"><?php echo $inactive_schools; ?></span>
            </div>
        </section>

        <section class="bg-white flex-1 rounded-3xl shadow-sm border border-slate-100 p-8 overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black uppercase tracking-tight">Recent Subscriptions</h2>
                <a href="#add_school" class="bg-indigo-600 text-white px-5 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-indigo-700 transition">Add New School</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="border-b border-slate-100">
                        <tr class="text-[10px] uppercase font-bold tracking-widest opacity-40">
                            <th class="pb-4 pt-2">School Code</th>
                            <th class="pb-4 pt-2">Institution Name</th>
                            <th class="pb-4 pt-2">Principal</th>
                            <th class="pb-4 pt-2">Status</th>
                            <th class="pb-4 pt-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach($schools as $school): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 font-mono font-bold uppercase"><?php echo htmlspecialchars($school['code']); ?></td>
                            <td class="py-4 font-bold text-slate-900"><?php echo htmlspecialchars($school['name']); ?></td>
                            <td class="py-4 font-medium opacity-80"><?php echo htmlspecialchars($school['principal_name']); ?></td>
                            <td class="py-4">
                                <span class="px-3 py-1 bg-<?php echo $school['status'] == 'ACTIVE' ? 'green' : 'slate'; ?>-100 text-<?php echo $school['status'] == 'ACTIVE' ? 'green' : 'slate'; ?>-700 rounded-full text-[10px] font-bold uppercase">
                                    <?php echo htmlspecialchars($school['status']); ?>
                                </span>
                            </td>
                            <td class="py-4 text-right font-bold text-indigo-600 cursor-pointer">Manage</td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(count($schools) == 0): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">No schools found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</body>
</html>
