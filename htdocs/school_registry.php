<?php
// school_registry.php
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ['SCHOOL_ADMIN', 'TEACHER'])) {
    header("location: index.php");
    exit;
}

$school_id = $_SESSION["school_id"];

// Handle Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_student' && $_SESSION['role'] == 'SCHOOL_ADMIN') {
    $name = trim($_POST['name']);
    $admission_number = trim($_POST['admission_number']);
    $dob = trim($_POST['dob']);
    $date_of_admission = trim($_POST['date_of_admission']);
    $assessment_number = trim($_POST['assessment_number']);
    $grade = trim($_POST['grade']);
    $stream = trim($_POST['stream']);

    $sql = "INSERT INTO students (school_id, admission_number, name, dob, date_of_admission, assessment_number, grade, stream) 
            VALUES (:school_id, :admission_number, :name, :dob, :date_of_admission, :assessment_number, :grade, :stream)";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":school_id", $school_id);
        $stmt->bindParam(":admission_number", $admission_number);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":dob", $dob);
        $stmt->bindParam(":date_of_admission", $date_of_admission);
        $stmt->bindParam(":assessment_number", $assessment_number);
        $stmt->bindParam(":grade", $grade);
        $stmt->bindParam(":stream", $stream);
        $stmt->execute();
    }
}

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && $_SESSION['role'] == 'SCHOOL_ADMIN') {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM students WHERE id = :id AND school_id = :school_id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $delete_id);
        $stmt->bindParam(":school_id", $school_id);
        $stmt->execute();
    }
}

// Fetch Students
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM students WHERE school_id = :school_id";
if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR admission_number LIKE :search)";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":school_id", $school_id);
if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bindParam(":search", $searchParam);
}
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Registry - EduCore</title>
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
            <a href="school_registry.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider bg-indigo-600 opacity-100">Learners</a>
            <a href="school_exams.php" class="flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider opacity-60 hover:opacity-100 hover:bg-white/10">Exams</a>
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
                <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-indigo-600 mb-2 block">Registry</span>
                <h1 class="text-3xl lg:text-4xl font-black tracking-tighter leading-none uppercase text-slate-900">Learners</h1>
            </div>
            <?php if ($_SESSION['role'] == 'SCHOOL_ADMIN'): ?>
            <button onclick="document.getElementById('add-form').classList.toggle('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors shrink-0">
                + Add Learner
            </button>
            <?php endif; ?>
        </header>

        <!-- Add Form -->
        <?php if ($_SESSION['role'] == 'SCHOOL_ADMIN'): ?>
        <div id="add-form" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-6 hidden">
            <h2 class="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-4 border-b border-slate-50 pb-2">New Learner Details</h2>
            <form action="school_registry.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="action" value="add_student">
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Full Name</label>
                    <input name="name" required class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Admission Number</label>
                    <input name="admission_number" required class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-mono font-bold bg-transparent transition-colors uppercase">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Assessment Number (KNEC)</label>
                    <input name="assessment_number" class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-mono font-bold bg-transparent transition-colors uppercase">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Date of Birth</label>
                    <input type="date" name="dob" required class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Date of Admission</label>
                    <input type="date" name="date_of_admission" required class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Grade</label>
                    <input name="grade" required placeholder="e.g. Grade 4" class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Stream</label>
                    <input name="stream" required placeholder="e.g. North" class="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                </div>
                <div class="md:col-span-3 mt-6 flex justify-end">
                    <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition-colors">Save Learner</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex-1 flex flex-col">
            <div class="p-6 border-b border-slate-100">
                <form action="school_registry.php" method="GET" class="relative w-full max-w-sm">
                    <input type="text" name="search" placeholder="Search learners..." value="<?php echo htmlspecialchars($search); ?>" class="w-full pl-5 pr-4 py-3 bg-slate-50 border-transparent rounded-full focus:ring-2 focus:ring-indigo-600 outline-none text-[11px] font-bold uppercase tracking-widest transition-all">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-indigo-600 uppercase">Search</button>
                </form>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left whitespace-nowrap border-collapse">
                    <thead class="border-b border-slate-100">
                        <tr class="text-[10px] uppercase font-bold tracking-widest opacity-40">
                            <th class="p-6">Adm No.</th>
                            <th class="p-6">Learner Name</th>
                            <th class="p-6">Grade/Stream</th>
                            <th class="p-6">Assessment #</th>
                            <th class="p-6">DOB</th>
                            <?php if ($_SESSION['role'] == 'SCHOOL_ADMIN'): ?><th class="p-6 text-right">Actions</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach($students as $student): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="p-6 font-mono font-bold uppercase"><?php echo htmlspecialchars($student['admission_number']); ?></td>
                            <td class="p-6 flex items-center font-bold text-slate-900">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-black mr-4 text-xs uppercase">
                                    <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                </div>
                                <?php echo htmlspecialchars($student['name']); ?>
                            </td>
                            <td class="p-6 font-medium opacity-80"><?php echo htmlspecialchars($student['grade'] . ' - ' . $student['stream']); ?></td>
                            <td class="p-6 font-mono font-medium opacity-80"><?php echo htmlspecialchars($student['assessment_number'] ? $student['assessment_number'] : '-'); ?></td>
                            <td class="p-6 font-medium opacity-80"><?php echo htmlspecialchars($student['dob']); ?></td>
                            <?php if ($_SESSION['role'] == 'SCHOOL_ADMIN'): ?>
                            <td class="p-6 text-right">
                                <a href="?delete=<?php echo $student['id']; ?>" onclick="return confirm('Delete this learner?');" class="text-rose-400 hover:text-rose-600 hover:bg-rose-50 px-3 py-2 rounded-full text-xs font-bold uppercase transition-colors">Delete</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(count($students) === 0): ?>
                        <tr><td colspan="6" class="p-12 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">No learners found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
