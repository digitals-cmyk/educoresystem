import { Routes, Route, Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../lib/auth-context';
import { LayoutDashboard, Users, BookOpen, Clock, Calendar, CheckSquare, MessageSquare, CreditCard, LogOut, Menu, X, MoreHorizontal } from 'lucide-react';
import { useState } from 'react';
import { SchoolRegistry } from '../pages/SchoolRegistry';
import { SchoolExams } from '../pages/SchoolExams';

function SchoolHome() {
  return (
    <div className="space-y-6">
      <div className="flex flex-col mb-12">
        <span className="text-[10px] uppercase tracking-[0.3em] font-bold text-indigo-600 mb-2">School Portal</span>
        <h1 className="text-7xl font-black tracking-tighter leading-none uppercase text-slate-900">Dashboard</h1>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">Total Students</span>
          <span className="text-8xl font-black tracking-tighter leading-none text-slate-900">450</span>
        </div>
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">Total Classes</span>
          <span className="text-8xl font-black tracking-tighter leading-none text-slate-900">12</span>
        </div>
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">Total Exams</span>
          <span className="text-8xl font-black tracking-tighter leading-none text-slate-900">4</span>
        </div>
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
          <span className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-1 text-slate-900">System Activity</span>
          <span className="text-6xl font-black tracking-tighter leading-none text-indigo-600 mt-2">Online</span>
        </div>
      </div>
    </div>
  )
}

export function SchoolLayout() {
  const { logout, profile } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const navItems = [
    { name: 'Dashboard', path: '/school', icon: <LayoutDashboard className="w-5 h-5 mr-3" /> },
    { name: 'Registry', path: '/school/registry', icon: <Users className="w-5 h-5 mr-3" /> },
    { name: 'Attendance', path: '/school/attendance', icon: <CheckSquare className="w-5 h-5 mr-3" /> },
    { name: 'Exams', path: '/school/exams', icon: <BookOpen className="w-5 h-5 mr-3" /> },
    { name: 'Timetable', path: '/school/timetable', icon: <Calendar className="w-5 h-5 mr-3" /> },
    { name: 'eLearning', path: '/school/elearning', icon: <Clock className="w-5 h-5 mr-3" /> },
    { name: 'Library', path: '/school/library', icon: <BookOpen className="w-5 h-5 mr-3" /> },
    { name: 'Messages', path: '/school/messages', icon: <MessageSquare className="w-5 h-5 mr-3" /> },
    { name: 'Fees', path: '/school/fees', icon: <CreditCard className="w-5 h-5 mr-3" /> },
  ];

  // Mobile bottom bar items
  const bottomNavItems = [
    { name: 'Home', path: '/school', icon: <LayoutDashboard className="w-6 h-6" /> },
    { name: 'Exams', path: '/school/exams', icon: <BookOpen className="w-6 h-6" /> },
    { name: 'Timetable', path: '/school/timetable', icon: <Calendar className="w-6 h-6" /> },
    { name: 'eLearning', path: '/school/elearning', icon: <Clock className="w-6 h-6" /> },
    { name: 'More', path: '#', icon: <MoreHorizontal className="w-6 h-6" />, isMenuTrigger: true },
  ];

  return (
    <div className="min-h-screen bg-slate-50 flex flex-col md:flex-row pb-16 md:pb-0 font-sans text-slate-900">
      {/* Mobile Top Bar */}
      <div className="md:hidden bg-slate-900 px-4 py-3 flex justify-between items-center shadow-sm border-b border-slate-800 sticky top-0 z-30 text-white">
        <div className="font-bold text-lg uppercase tracking-wider">EduCore</div>
        <button onClick={() => setMobileMenuOpen(!mobileMenuOpen)} className="p-2 opacity-60">
          {mobileMenuOpen ? <X /> : <Menu />}
        </button>
      </div>

      {/* Desktop Sidebar OR Mobile Full Menu */}
      <div className={`\${mobileMenuOpen ? 'flex' : 'hidden'} md:flex fixed inset-0 md:relative z-50 w-full md:w-64 bg-slate-900 flex-col text-white min-h-screen p-8`}>
        <div className="font-bold text-2xl hidden md:flex mb-10"><div className="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center font-bold text-xl uppercase leading-none">E</div></div>
        <div className="text-[10px] uppercase tracking-widest font-bold opacity-40 mb-4 mt-2">
          {profile?.role?.replace('_', ' ')}
        </div>
        <nav className="flex-1 flex flex-col gap-2 overflow-y-auto">
          {navItems.map((item) => {
            const isActive = location.pathname === item.path || (item.path !== '/school' && location.pathname.startsWith(item.path));
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
        <div className="pt-4 mt-auto hidden md:block">
           <div className="px-4 py-3 text-[10px] font-bold uppercase tracking-wider opacity-60 truncate mb-2">
            {profile?.name}
          </div>
          <button onClick={handleLogout} className="flex items-center w-full px-4 py-3 text-xs font-bold uppercase tracking-wider text-rose-400 hover:bg-rose-500/10 rounded-full transition-colors">
            <LogOut className="w-5 h-5 mr-3" />
            Sign Out
          </button>
        </div>
      </div>

      {/* Main Content Area */}
      <div className="flex-1 p-4 md:p-10 overflow-x-hidden">
        <Routes>
          <Route path="/" element={<SchoolHome />} />
          <Route path="/registry/*" element={<SchoolRegistry />} />
          <Route path="/attendance/*" element={<div>Attendance Module</div>} />
          <Route path="/exams/*" element={<SchoolExams />} />
          <Route path="/timetable/*" element={<div>Timetable Module</div>} />
          <Route path="/elearning/*" element={<div>eLearning Module</div>} />
          <Route path="/library/*" element={<div>Library Module</div>} />
          <Route path="/messages/*" element={<div>Messages Module</div>} />
          <Route path="/fees/*" element={<div>Fees Module</div>} />
        </Routes>
      </div>

      {/* Mobile Bottom Navigation */}
      <div className="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 flex justify-around items-center z-30 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
         {bottomNavItems.map((item, idx) => {
           const isActive = location.pathname === item.path;
           return item.isMenuTrigger ? (
             <button
                key={idx}
                onClick={() => setMobileMenuOpen(true)}
                className={`flex flex-col items-center py-3 px-2 text-[10px] font-bold uppercase tracking-widest text-slate-500`}
              >
                {item.icon}
                <span className="mt-1">{item.name}</span>
              </button>
           ) : (
            <Link
              key={idx}
              to={item.path}
              className={`flex flex-col items-center py-3 px-2 text-[10px] font-bold uppercase tracking-widest transition-colors \${
                isActive ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-900'
              }`}
            >
              <div className={isActive ? 'bg-indigo-50 rounded-full p-2' : 'p-2'}>
                {item.icon}
              </div>
              <span className="mt-1">{item.name}</span>
            </Link>
           )
         })}
      </div>
    </div>
  );
}
