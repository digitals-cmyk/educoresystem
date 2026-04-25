<?php
// school_exams.php
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['SCHOOL_ADMIN', 'TEACHER'])) {
    header("location: index.php");
    exit;
}

$school_id = $_SESSION["school_id"];

// Handle Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_exam' && in_array($_SESSION["role"], ['SCHOOL_ADMIN', 'TEACHER'])) {
    $name = trim($_POST['name']);
    $term = trim($_POST['term']);
    $year = trim($_POST['year']);

    $sql = "INSERT INTO exams (school_id, name, term, year, status) VALUES (:school_id, :name, :term, :year, 'UNPUBLISHED')";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":school_id", $school_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":term", $term);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
    }
}

// Handle Publish Toggle
if (isset($_GET['toggle_publish']) && is_numeric($_GET['toggle_publish']) && in_array($_SESSION["role"], ['SCHOOL_ADMIN', 'TEACHER'])) {
    $exam_id = $_GET['toggle_publish'];
    
    // get current status
    $sql = "SELECT status FROM exams WHERE id = :id AND school_id = :school_id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $exam_id);
        $stmt->bindParam(":school_id", $school_id);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $new_status = $row['status'] == 'PUBLISHED' ? 'UNPUBLISHED' : 'PUBLISHED';
            $update_sql = "UPDATE exams SET status = :status WHERE id = :id";
            if ($up_stmt = $pdo->prepare($update_sql)) {
                $up_stmt->bindParam(":status", $new_status);
                $up_stmt->bindParam(":id", $exam_id);
                $up_stmt->execute();
            }
        }
    }
    header("Location: school_exams.php");
    exit;
}

// Fetch Exams
$sql = "SELECT * FROM exams WHERE school_id = :school_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":school_id", $school_id);
$stmt->execute();
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams Management - EduCore</title>
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
            <a href="school_admin.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider opacity-60 hover:opacity-100 hover:bg-white/10">Dashboard</a>
            <a href="school_registry.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider opacity-60 hover:opacity-100 hover:bg-white/10">Learners</a>
            <a href="school_exams.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider bg-indigo-600 opacity-100">Exams</a>
        </nav>
        <div class="pt-4 mt-auto hidden lg:block">
            <div class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider opacity-60 truncate mb-2"><?php echo htmlspecialchars($_SESSION["name"]); ?></div>
            <a href="logout.php" class="flex items-center w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-rose-400 hover:bg-rose-500/10 rounded-full transition-colors">Sign Out</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col p-6 lg:p-10 overflow-auto">
        <header class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
            <div>
                <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-indigo-600 mb-2 block">Assessments</span>
                <h1 class="text-3xl lg:text-4xl font-black tracking-tighter leading-none uppercase text-slate-900">Exams Management</h1>
            </div>
            <?php if (in_array($_SESSION['role'], ['SCHOOL_ADMIN', 'TEACHER'])): ?>
            <button onclick="document.getElementById('add-form').classList.toggle('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors shrink-0">
                + Create Exam
            </button>
            <?php endif; ?>
        </header>

        <!-- Add Form -->
        <?php if (in_array($_SESSION['role'], ['SCHOOL_ADMIN', 'TEACHER'])): ?>
        <div id="add-form" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-6 hidden">
            <h2 class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-4 border-b border-slate-50 pb-2">New Exam Details</h2>
            <form action="school_exams.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="action" value="add_exam">
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Exam Name</label>
                    <input name="name" required placeholder="e.g. Mid Term" class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Term</label>
                    <select name="term" required class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                        <option value="Term 1">Term 1</option>
                        <option value="Term 2">Term 2</option>
                        <option value="Term 3">Term 3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Year</label>
                    <input type="number" name="year" required value="<?php echo date('Y'); ?>" class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-mono font-bold bg-transparent transition-colors">
                </div>
                <div class="md:col-span-3 mt-6 flex justify-end">
                    <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition-colors">Create Exam</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if(count($exams) === 0): ?>
        <div class="text-center p-12 bg-white rounded-3xl shadow-sm border border-slate-100">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">📋</div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">No exams created yet.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($exams as $exam): ?>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-slate-900"><?php echo htmlspecialchars($exam['name']); ?></h3>
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-40 mt-1"><?php echo htmlspecialchars($exam['term'] . ' - ' . $exam['year']); ?></p>
                    </div>
                    <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full tracking-widest <?php echo $exam['status'] == 'PUBLISHED' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'; ?>">
                        <?php echo htmlspecialchars($exam['status']); ?>
                    </span>
                </div>
                
                <div class="space-y-3 mt-auto pt-6 border-t border-slate-50">
                    <button class="w-full flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition-colors">
                        📝 Marks Entry
                    </button>
                    <button class="w-full flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition-colors">
                        📊 View Merit List
                    </button>
                    
                    <?php if (in_array($_SESSION['role'], ['SCHOOL_ADMIN', 'TEACHER'])): ?>
                    <a href="?toggle_publish=<?php echo $exam['id']; ?>" class="block text-center w-full border-2 border-slate-100 mt-4 hover:border-indigo-600 hover:text-indigo-600 text-slate-500 font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition-colors">
                        <?php echo $exam['status'] == 'PUBLISHED' ? 'Unpublish Results' : 'Publish Results'; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

</body>
</html>
