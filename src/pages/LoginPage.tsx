import React, { useState } from 'react';
import { signInWithPopup, GoogleAuthProvider, sendPasswordResetEmail } from 'firebase/auth';
import { auth } from '../lib/firebase';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../lib/auth-context';
import { Lock, User, AtSign, Loader2, ArrowLeft } from 'lucide-react';

export function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [isReset, setIsReset] = useState(false);
  const [resetSent, setResetSent] = useState(false);
  const navigate = useNavigate();
  const { profile } = useAuth();

  // Redirect if already logged in
  if (profile) {
    if (profile.role === 'SUPER_ADMIN') {
      navigate('/super-admin');
    } else {
      navigate('/school');
    }
  }

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      const provider = new GoogleAuthProvider();
      await signInWithPopup(auth, provider);
      // Let the auth listener handle redirection to protected routes
    } catch (err: any) {
      console.error(err);
      if (err.message?.includes('network-request-failed')) {
        setError('Network request failed. Please check your connection or disable ad-blockers. If using AI Studio, try opening the app in a new tab.');
      } else {
        setError(err.message || 'Invalid login details');
      }
      setLoading(false);
    }
  };

  const handleReset = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      await sendPasswordResetEmail(auth, email);
      setResetSent(true);
    } catch (err: any) {
      setError(err.message || 'Failed to send reset email');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-900">
      <div className="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 w-full max-w-md">
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-indigo-600 rounded-lg mx-auto flex items-center justify-center mb-4">
            <Lock className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-3xl font-black uppercase tracking-tight text-slate-900">EduCore System</h1>
          <p className="text-[11px] font-bold uppercase tracking-widest text-slate-500 mt-2 opacity-60">
            Multi-School Management Platform
          </p>
        </div>

        {error && (
          <div className="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
            {error}
          </div>
        )}

        {!isReset ? (
          <div className="space-y-4">
            <button
              disabled={loading}
              onClick={handleLogin}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest py-4 rounded-full transition-colors flex justify-center mt-6 disabled:opacity-70 disabled:cursor-not-allowed"
            >
              {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : 'Log In With Google'}
            </button>
            <p className="text-xs text-center text-gray-500 mt-4 leading-relaxed">
              By logging in, you agree to the Terms of Service.
            </p>
          </div>
        ) : (
          <form onSubmit={handleReset} className="space-y-4">
             <div className="mb-6">
              <button type="button" onClick={() => setIsReset(false)} className="text-[10px] uppercase font-bold tracking-widest text-slate-500 flex items-center hover:text-slate-900 transition-colors">
                <ArrowLeft className="w-3 h-3 mr-2" /> Back to Login
              </button>
            </div>
            
            {resetSent ? (
               <div className="bg-green-50 text-green-700 p-4 rounded-xl text-xs font-bold uppercase tracking-wider text-center border border-green-100">
                 Password reset email sent! Please check your inbox.
               </div>
            ) : (
              <>
                <div>
                  <label className="block text-xs font-bold uppercase tracking-widest text-slate-700 mb-2 opacity-60">Registered Email</label>
                  <div className="relative">
                    <AtSign className="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition-all font-medium text-sm"
                      placeholder="Enter your email"
                    />
                  </div>
                </div>

                <button
                  disabled={loading}
                  type="submit"
                  className="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest py-4 rounded-full transition-colors flex justify-center mt-6 disabled:opacity-70 disabled:cursor-not-allowed"
                >
                  {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : 'Send Reset Link'}
                </button>
              </>
            )}
          </form>
        )}
      </div>

      <div className="fixed bottom-4 left-0 right-0 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
        <p>License Expiry: <span className="text-rose-500">31st Dec 2026</span></p>
      </div>
    </div>
  );
}
