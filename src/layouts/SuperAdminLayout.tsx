import { Routes, Route, Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../lib/auth-context';
import { LayoutDashboard, LogOut, School as SchoolIcon, Shield, Menu, X } from 'lucide-react';
import { useState } from 'react';
import { SuperAdminSchools } from '../pages/SuperAdminSchools';

function SuperAdminHome() {
  return (
    <div className="space-y-6">
      <div className="flex flex-col mb-12">
        <span className="text-[10px] uppercase tracking-[0.3em] font-bold text-indigo-600 mb-2">Super Admin Portal</span>
        <h1 className="text-7xl font-black tracking-tighter leading-none uppercase text-slate-900">System Overview</h1>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">Total Schools</span>
          <span className="text-8xl font-black tracking-tighter leading-none text-slate-900">10</span>
        </div>
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">Active Schools</span>
          <span className="text-8xl font-black tracking-tighter leading-none text-indigo-600">8</span>
        </div>
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">Inactive Schools</span>
          <span className="text-8xl font-black tracking-tighter leading-none text-slate-900">2</span>
        </div>
      </div>
    </div>
  )
}

export function SuperAdminLayout() {
  const { logout, profile } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const menu = [
    { name: 'Dashboard', path: '/super-admin', icon: <LayoutDashboard className="w-5 h-5 mr-3" /> },
    { name: 'Manage Schools', path: '/super-admin/schools', icon: <SchoolIcon className="w-5 h-5 mr-3" /> },
    { name: 'Manage Sub-Admins', path: '/super-admin/subadmins', icon: <Shield className="w-5 h-5 mr-3" /> },
  ];

  return (
    <div className="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-900">
      <div className="md:hidden bg-slate-900 px-4 py-3 flex justify-between items-center shadow-sm border-b text-white">
        <div className="font-bold text-lg">EduCore Super</div>
        <button onClick={() => setMobileMenuOpen(!mobileMenuOpen)} className="p-2 opacity-60">
          {mobileMenuOpen ? <X /> : <Menu />}
        </button>
      </div>

      <div className={`\${mobileMenuOpen ? 'flex' : 'hidden'} md:flex w-full md:w-64 bg-slate-900 min-h-screen p-8 flex-col text-white`}>
        <div className="font-bold text-2xl hidden md:block mb-10"><div className="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center font-bold text-xl uppercase leading-none">E</div></div>
        <div className="text-[10px] uppercase tracking-widest font-bold opacity-40 mb-4 mt-2">Super Admin</div>
        <nav className="flex-1 flex flex-col gap-2">
          {menu.map((item) => {
            const isActive = location.pathname === item.path;
            return (
              <Link
                key={item.name}
                to={item.path}
                className={`flex items-center px-4 py-3 rounded-full text-sm font-bold transition-all uppercase tracking-wider \${
                  isActive ? 'bg-indigo-600 opacity-100' : 'opacity-60 hover:opacity-100 hover:bg-white/10'
                }`}
                onClick={() => setMobileMenuOpen(false)}
              >
                {item.icon}
                {item.name}
              </Link>
            )
          })}
        </nav>
        <div className="pt-4 mt-auto">
          <div className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider opacity-60 break-words mb-2">
            {profile?.name}
          </div>
          <button onClick={handleLogout} className="flex items-center w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-rose-400 hover:bg-rose-500/10 rounded-full transition-colors">
            <LogOut className="w-5 h-5 mr-3" />
            Sign Out
          </button>
        </div>
      </div>

      <div className="flex-1 p-4 md:p-10 overflow-auto">
        <Routes>
           <Route path="/" element={<SuperAdminHome />} />
           <Route path="/schools" element={<SuperAdminSchools />} />
           <Route path="/subadmins" element={<div>Manage Sub-Admins Module (Coming Soon)</div>} />
        </Routes>
      </div>
    </div>
  );
}
