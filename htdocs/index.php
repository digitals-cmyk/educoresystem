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
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 w-full max-w-md relative overflow-hidden">
        <!-- Decorative blob -->
        <div class="absolute -top-24 -right-24 w-56 h-56 bg-indigo-50 rounded-full blur-3xl opacity-60"></div>
        
        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-600/30 mx-auto flex items-center justify-center mb-6 text-2xl text-white">
                <span class="font-black italic">E</span>
            </div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-900">EduCore</h1>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-2">School Management System</p>
        </div>

        <?php 
        if(!empty($login_err)){
            echo '<div class="bg-rose-50 text-rose-600 p-4 rounded-2xl mb-6 text-[11px] font-bold uppercase tracking-wider text-center border border-rose-100">' . $login_err . '</div>';
        }
        if(!empty($reset_msg)){
            echo '<div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 text-[11px] font-bold uppercase tracking-wider text-center border border-emerald-100">' . $reset_msg . '</div>';
        }
        ?>

        <!-- LOGIN FORM -->
        <form id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5 relative z-10 <?php echo (!empty($reset_msg)) ? 'hidden' : 'block'; ?>">
            <input type="hidden" name="action" value="login">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Username / Email</label>
                <input type="text" name="email" required value="<?php echo htmlspecialchars($email); ?>" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 focus:bg-white rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300" placeholder="admin@domain.com">
                <span class="text-[10px] uppercase font-bold tracking-widest text-rose-500 mt-2 block"><?php echo $email_err; ?></span>
            </div>    
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Password</label>
                    <button type="button" onclick="toggleForms()" class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors">Forgot Password?</button>
                </div>
                <input type="password" name="password" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 focus:bg-white rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300" placeholder="••••••••">
                <span class="text-[10px] uppercase font-bold tracking-widest text-rose-500 mt-2 block"><?php echo $password_err; ?></span>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-widest py-4 rounded-full transition-all shadow-lg hover:-translate-y-0.5 active:translate-y-0">Secure Sign In</button>
            </div>
        </form>

        <!-- FORGOT PASSWORD FORM -->
        <form id="reset-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5 relative z-10 <?php echo (!empty($reset_msg)) ? 'block' : 'hidden'; ?>">
            <input type="hidden" name="action" value="reset">
            <div class="text-center mb-6">
                <p class="text-xs font-medium text-slate-500 leading-relaxed">Enter your registered email address to receive password reset instructions.</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Email Address</label>
                <input type="email" name="reset_email" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 focus:bg-white rounded-2xl focus:border-indigo-600 outline-none transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300" placeholder="user@domain.com">
            </div>
            <div class="pt-4 space-y-3">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest py-4 rounded-full transition-all shadow-lg shadow-indigo-600/30 hover:-translate-y-0.5 active:translate-y-0">Send Reset Link</button>
                <button type="button" onclick="toggleForms()" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-widest py-4 rounded-full transition-all">Back to Login</button>
            </div>
        </form>
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
