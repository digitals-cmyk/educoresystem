<?php
// index.php
require_once "config.php";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["role"] == "SUPER_ADMIN") {
        header("location: super_admin.php");
    } else {
        header("location: school_admin.php");
    }
    exit;
}

$email = $password = "";
$email_err = $password_err = $login_err = $reset_msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login'){
    
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter username/email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($email_err) && empty($password_err)){
        $sql = "SELECT id, name, username, email, password, role, school_id FROM users WHERE (email = :email OR username = :email) AND status = 'ACTIVE'";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $name = $row["name"];
                        $email = $row["email"];
                        $hashed_password = $row["password"];
                        $role = $row["role"];
                        $school_id = $row["school_id"];
                        
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["name"] = $name;
                            $_SESSION["email"] = $email;
                            $_SESSION["role"] = $role;
                            $_SESSION["school_id"] = $school_id;
                            
                            if($role == 'SUPER_ADMIN'){
                                header("location: super_admin.php");
                            } else {
                                header("location: school_admin.php");
                            }
                        } else{
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid email or password.";
                }
            } else{
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'reset') {
    $reset_msg = "If your email is in our system, you will receive a password reset link shortly.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduCore System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex">
    
    <!-- Left Side Content/Image -->
    <div class="hidden lg:flex w-1/2 bg-slate-900 p-12 relative overflow-hidden flex-col justify-between">
        <!-- Abstract background graphics -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-indigo-600 blur-[120px]"></div>
            <div class="absolute top-[60%] -right-[20%] w-[60%] h-[60%] rounded-full bg-emerald-500 blur-[120px]"></div>
        </div>
        
        <!-- Optional Image specific element (using a placeholder that looks good or unsplash) -->
        <div class="absolute inset-0 z-0 opacity-40 mix-blend-overlay" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2670&auto=format&fit=crop'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-xl text-white">
                <span class="font-black italic">E</span>
            </div>
            <span class="text-xl font-black uppercase tracking-tight text-white">EduCore</span>
        </div>

        <div class="relative z-10 max-w-lg mt-auto pb-12">
            <h2 class="text-5xl font-black text-white tracking-tighter leading-[1.1] mb-6 uppercase">Modern<br>School<br>Management.</h2>
            <p class="text-slate-400 font-medium leading-relaxed mb-8">A comprehensive institutional core designed for next-generation administration, bringing total visibility and control to your workflow.</p>
            <div class="flex gap-4">
                <div class="flex -space-x-4">
                    <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://ui-avatars.com/api/?name=JS&background=6366f1&color=fff" alt="">
                    <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://ui-avatars.com/api/?name=AK&background=10b981&color=fff" alt="">
                    <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://ui-avatars.com/api/?name=MD&background=f59e0b&color=fff" alt="">
                </div>
                <div class="flex flex-col justify-center">
                    <span class="text-white font-bold text-sm">Trusted by</span>
                    <span class="text-slate-400 text-xs font-semibold uppercase tracking-widest text-[10px]">100+ Institutions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Header (Hidden on Desktop) -->
            <div class="text-center mb-10 lg:hidden display-flex flex-col align-center items-center">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-600/30 mx-auto flex items-center justify-center mb-4 text-2xl text-white">
                    <span class="font-black italic">E</span>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-tight text-slate-900">EduCore</h1>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-2">School Management</p>
            </div>
            
            <!-- Desktop Header -->
            <div class="hidden lg:block mb-10">
                <h1 class="text-4xl font-black uppercase tracking-tight text-slate-900 mb-2">Welcome Back</h1>
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 opacity-80">Sign in to your account to continue</p>
            </div>

            <?php 
            if(!empty($login_err)){
                echo '<div class="bg-rose-50 text-rose-600 p-4 rounded-2xl mb-8 text-[11px] font-bold uppercase tracking-wider text-center border border-rose-100 shadow-sm">' . $login_err . '</div>';
            }
            if(!empty($reset_msg)){
                echo '<div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-8 text-[11px] font-bold uppercase tracking-wider text-center border border-emerald-100 shadow-sm">' . $reset_msg . '</div>';
            }
            ?>

            <!-- LOGIN FORM -->
            <form id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6 <?php echo (!empty($reset_msg)) ? 'hidden' : 'block'; ?>">
                <input type="hidden" name="action" value="login">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Username / Email</label>
                    <input type="text" name="email" required value="<?php echo htmlspecialchars($email); ?>" class="w-full px-5 py-4 bg-white border-2 border-slate-100 focus:bg-white rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300 shadow-sm" placeholder="admin@domain.com">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-rose-500 mt-2 block"><?php echo $email_err; ?></span>
                </div>    
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Password</label>
                        <button type="button" onclick="toggleForms()" class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors">Forgot Password?</button>
                    </div>
                    <input type="password" name="password" required class="w-full px-5 py-4 bg-white border-2 border-slate-100 focus:bg-white rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300 shadow-sm" placeholder="••••••••">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-rose-500 mt-2 block"><?php echo $password_err; ?></span>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-widest py-4 sm:py-5 rounded-2xl transition-all shadow-lg hover:-translate-y-0.5 active:translate-y-0">Sign In</button>
                </div>
            </form>

            <!-- FORGOT PASSWORD FORM -->
            <form id="reset-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6 <?php echo (!empty($reset_msg)) ? 'block' : 'hidden'; ?>">
                <input type="hidden" name="action" value="reset">
                <div class="mb-6">
                    <p class="text-sm font-medium text-slate-500 leading-relaxed">Enter your registered email address to receive password reset instructions.</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Email Address</label>
                    <input type="email" name="reset_email" required class="w-full px-5 py-4 bg-white border-2 border-slate-100 focus:bg-white rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300 shadow-sm" placeholder="user@domain.com">
                </div>
                <div class="pt-4 space-y-3">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest py-4 sm:py-5 rounded-2xl transition-all shadow-lg shadow-indigo-600/30 hover:-translate-y-0.5 active:translate-y-0">Send Reset Link</button>
                    <button type="button" onclick="toggleForms()" class="w-full bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase tracking-widest py-4 rounded-2xl transition-all">Back to Login</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const resetForm = document.getElementById('reset-form');
            if (loginForm.classList.contains('hidden')) {
                loginForm.classList.remove('hidden');
                resetForm.classList.add('hidden');
            } else {
                loginForm.classList.add('hidden');
                resetForm.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
