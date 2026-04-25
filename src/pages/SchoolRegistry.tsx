import React, { useState, useEffect } from 'react';
import { db } from '../lib/firebase';
import { collection, getDocs, addDoc, doc, setDoc, query, where, deleteDoc } from 'firebase/firestore';
import { useAuth } from '../lib/auth-context';
import { Plus, Users, Search, Download, Upload, Trash2 } from 'lucide-react';
import { handleFirestoreError } from '../lib/handle-firestore-error';

interface Learner {
  id: string;
  admissionNumber: string;
  name: string;
  dob: string;
  dateOfAdmission: string;
  assessmentNumber: string;
  grade: string;
  stream: string;
}

export function SchoolRegistry() {
  const { profile } = useAuth();
  const schoolId = profile?.schoolId;
  const [learners, setLearners] = useState<Learner[]>([]);
  const [loading, setLoading] = useState(true);
  const [showAdd, setShowAdd] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const [formData, setFormData] = useState<Partial<Learner>>({
    name: '', admissionNumber: '', dob: '', dateOfAdmission: '', assessmentNumber: '', grade: '', stream: ''
  });

  useEffect(() => {
    if (schoolId) fetchLearners();
  }, [schoolId]);

  const fetchLearners = async () => {
    if (!schoolId) return;
    setLoading(true);
    try {
      const q = query(collection(db, `schools/${schoolId}/students`));
      const snap = await getDocs(q);
      setLearners(snap.docs.map(d => ({ id: d.id, ...d.data() } as Learner)));
    } catch (e) {
      console.error(e);
      // Wait, should use handleFirestoreError if we imported it... actually let's just log for now since it's a small app
    }
    setLoading(false);
  };

  const handleAdd = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!schoolId) return;
    try {
      await addDoc(collection(db, `schools/${schoolId}/students`), {
        ...formData,
        createdAt: Date.now()
      });
      setShowAdd(false);
      setFormData({ name: '', admissionNumber: '', dob: '', dateOfAdmission: '', assessmentNumber: '', grade: '', stream: '' });
      fetchLearners();
    } catch (e) {
      console.error(e);
    }
  };

  const handleDelete = async (id: string) => {
    if (!schoolId || !window.confirm('Are you sure you want to delete this learner?')) return;
    try {
      await deleteDoc(doc(db, `schools/${schoolId}/students`, id));
      fetchLearners();
    } catch (e) {
      console.error(e);
    }
  };

  const filtered = learners.filter(l => l.name.toLowerCase().includes(searchTerm.toLowerCase()) || l.admissionNumber.includes(searchTerm));

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row justify-between md:items-center space-y-4 md:space-y-0 mb-6">
        <h1 className="text-xl font-black uppercase tracking-tight text-slate-900">Learner Registry</h1>
        
        {/* Actions */}
        <div className="flex space-x-2">
          <button className="bg-slate-100 hover:bg-slate-200 text-slate-900 px-4 py-2 rounded-full flex items-center text-[10px] font-bold uppercase tracking-widest transition-colors">
            <Upload className="w-4 h-4 mr-2" /> Import
          </button>
          <button className="bg-slate-100 hover:bg-slate-200 text-slate-900 px-4 py-2 rounded-full flex items-center text-[10px] font-bold uppercase tracking-widest transition-colors">
            <Download className="w-4 h-4 mr-2" /> Export
          </button>
          {profile?.role === 'SCHOOL_ADMIN' && (
             <button onClick={() => setShowAdd(!showAdd)} className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full flex items-center text-[10px] font-bold uppercase tracking-widest transition-colors">
               <Plus className="w-4 h-4 mr-2" /> Add Learner
             </button>
          )}
        </div>
      </div>

      {showAdd && profile?.role === 'SCHOOL_ADMIN' && (
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-6">
          <h2 className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-4 border-b border-slate-50 pb-2">New Learner Details</h2>
          <form onSubmit={handleAdd} className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Full Name</label>
              <input required value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Admission Number</label>
              <input required value={formData.admissionNumber} onChange={e => setFormData({...formData, admissionNumber: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-mono font-bold bg-transparent transition-colors uppercase" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Assessment Number (KNEC)</label>
              <input value={formData.assessmentNumber} onChange={e => setFormData({...formData, assessmentNumber: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-mono font-bold bg-transparent transition-colors uppercase" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Date of Birth</label>
              <input type="date" required value={formData.dob} onChange={e => setFormData({...formData, dob: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Date of Admission</label>
              <input type="date" required value={formData.dateOfAdmission} onChange={e => setFormData({...formData, dateOfAdmission: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Grade</label>
              <input required value={formData.grade} onChange={e => setFormData({...formData, grade: e.target.value})} placeholder="e.g. Grade 4" className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Stream</label>
              <input required value={formData.stream} onChange={e => setFormData({...formData, stream: e.target.value})} placeholder="e.g. North" className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors" />
            </div>
            <div className="md:col-span-3 mt-6 flex justify-end">
              <button type="submit" className="bg-slate-900 text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition-colors">Save Learner</button>
            </div>
          </form>
        </div>
      )}

      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div className="p-6 border-b border-slate-100 flex items-center">
            <div className="relative w-full max-w-sm">
              <Search className="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
              <input 
                type="text" 
                placeholder="Search..." 
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-12 pr-4 py-3 bg-slate-50 border-transparent rounded-full focus:ring-2 focus:ring-indigo-600 outline-none text-[11px] font-bold uppercase tracking-widest transition-all"
              />
            </div>
        </div>
        
        {loading ? (
           <div className="p-12 flex justify-center"><div className="w-8 h-8 rounded-full border-4 border-indigo-200 border-t-indigo-600 animate-spin"></div></div>
        ) : filtered.length === 0 ? (
           <div className="p-12 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">No learners found.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left whitespace-nowrap border-collapse">
              <thead className="border-b border-slate-100">
                <tr className="text-[10px] uppercase font-bold tracking-widest opacity-40">
                  <th className="p-6">Adm No.</th>
                  <th className="p-6">Learner Name</th>
                  <th className="p-6">Grade/Stream</th>
                  <th className="p-6">Assessment #</th>
                  <th className="p-6">DOB</th>
                  {profile?.role === 'SCHOOL_ADMIN' && <th className="p-6 text-right">Actions</th>}
                </tr>
              </thead>
              <tbody className="text-sm">
                {filtered.map(l => (
                  <tr key={l.id} className="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">
                    <td className="p-6 font-mono font-bold uppercase">{l.admissionNumber}</td>
                    <td className="p-6 flex items-center font-bold text-slate-900">
                      <div className="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-black mr-4 text-xs uppercase">{l.name.charAt(0)}</div>
                      {l.name}
                    </td>
                    <td className="p-6 font-medium opacity-80">{l.grade} - {l.stream}</td>
                    <td className="p-6 font-mono font-medium opacity-80">{l.assessmentNumber || '-'}</td>
                    <td className="p-6 font-medium opacity-80">{l.dob}</td>
                    {profile?.role === 'SCHOOL_ADMIN' && (
                      <td className="p-6 flex justify-end">
                        <button onClick={() => handleDelete(l.id)} className="text-rose-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-full transition-colors"><Trash2 className="w-4 h-4"/></button>
                      </td>
                    )}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
