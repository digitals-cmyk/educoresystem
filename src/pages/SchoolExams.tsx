import React, { useState, useEffect } from 'react';
import { db } from '../lib/firebase';
import { collection, getDocs, addDoc, doc, setDoc, query, where, deleteDoc } from 'firebase/firestore';
import { useAuth } from '../lib/auth-context';
import { Plus, BookOpen, AlertCircle, FileText, CheckCircle } from 'lucide-react';

interface Exam {
  id: string;
  name: string;
  term: string;
  year: string;
  status: 'PUBLISHED' | 'UNPUBLISHED';
}

export function SchoolExams() {
  const { profile } = useAuth();
  const schoolId = profile?.schoolId;
  const [exams, setExams] = useState<Exam[]>([]);
  const [loading, setLoading] = useState(true);
  const [showAdd, setShowAdd] = useState(false);
  const [formData, setFormData] = useState<Partial<Exam>>({
    name: '', term: 'Term 1', year: new Date().getFullYear().toString(), status: 'UNPUBLISHED'
  });

  useEffect(() => {
    if (schoolId) fetchExams();
  }, [schoolId]);

  const fetchExams = async () => {
    if (!schoolId) return;
    setLoading(true);
    try {
      const q = query(collection(db, `schools/${schoolId}/exams`));
      const snap = await getDocs(q);
      setExams(snap.docs.map(d => ({ id: d.id, ...d.data() } as Exam)));
    } catch (e) {
      console.error(e);
    }
    setLoading(false);
  };

  const handleAdd = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!schoolId) return;
    try {
      await addDoc(collection(db, `schools/${schoolId}/exams`), {
        ...formData,
        createdAt: Date.now()
      });
      setShowAdd(false);
      setFormData({ name: '', term: 'Term 1', year: new Date().getFullYear().toString(), status: 'UNPUBLISHED' });
      fetchExams();
    } catch (e) {
      console.error(e);
    }
  };

  const publishExam = async (id: string, currentStatus: string) => {
    if (!schoolId) return;
    try {
      await setDoc(doc(db, `schools/${schoolId}/exams`, id), {
        status: currentStatus === 'PUBLISHED' ? 'UNPUBLISHED' : 'PUBLISHED'
      }, { merge: true });
      fetchExams();
    } catch (e) {
      console.error(e);
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-xl font-black uppercase tracking-tight text-slate-900">Exams Management</h1>
        {(profile?.role === 'SCHOOL_ADMIN' || profile?.role === 'TEACHER') && (
          <button onClick={() => setShowAdd(!showAdd)} className="bg-indigo-600 text-white px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider transition-colors hover:bg-indigo-700 shadow-sm flex items-center">
            <Plus className="w-4 h-4 mr-2" />
            Create Exam
          </button>
        )}
      </div>

      {showAdd && (
        <div className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col mb-6">
          <h2 className="text-[11px] uppercase font-bold tracking-widest opacity-40 mb-4 border-b border-slate-50 pb-2">New Exam Details</h2>
          <form onSubmit={handleAdd} className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Exam Name</label>
              <input required value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors" placeholder="e.g. Mid Term" />
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Term</label>
              <select required value={formData.term} onChange={e => setFormData({...formData, term: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-bold bg-transparent transition-colors">
                <option value="Term 1">Term 1</option>
                <option value="Term 2">Term 2</option>
                <option value="Term 3">Term 3</option>
              </select>
            </div>
            <div>
              <label className="block text-[10px] uppercase font-bold tracking-widest opacity-40 mb-1">Year</label>
              <input required type="number" value={formData.year} onChange={e => setFormData({...formData, year: e.target.value})} className="w-full border-b-2 border-slate-100 focus:border-indigo-600 outline-none p-2 font-mono font-bold bg-transparent transition-colors" />
            </div>
            <div className="md:col-span-3 mt-6 flex justify-end">
              <button type="submit" className="bg-slate-900 text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition-colors">Create Exam</button>
            </div>
          </form>
        </div>
      )}

      {loading ? (
        <div className="p-12 flex justify-center"><div className="w-8 h-8 rounded-full border-4 border-indigo-200 border-t-indigo-600 animate-spin"></div></div>
      ) : exams.length === 0 ? (
        <div className="text-center p-12 bg-white rounded-3xl shadow-sm border border-slate-100">
          <AlertCircle className="w-12 h-12 text-slate-300 mx-auto mb-3" />
          <p className="text-[10px] font-bold uppercase tracking-widest text-slate-400">No exams created yet.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {exams.map(exam => (
            <div key={exam.id} className="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
              <div className="flex justify-between items-start mb-6">
                <div>
                  <h3 className="text-xl font-black uppercase tracking-tight text-slate-900">{exam.name}</h3>
                  <p className="text-[10px] font-bold uppercase tracking-widest opacity-40 mt-1">{exam.term} - {exam.year}</p>
                </div>
                <span className={`px-3 py-1 text-[10px] font-bold uppercase rounded-full tracking-widest \${exam.status === 'PUBLISHED' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'}`}>
                  {exam.status}
                </span>
              </div>
              
              <div className="space-y-3 mt-auto pt-6 border-t border-slate-50">
                <button className="w-full flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition-colors">
                  <FileText className="w-4 h-4 mr-2" /> Marks Entry
                </button>
                <button className="w-full flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition-colors">
                  <BookOpen className="w-4 h-4 mr-2" /> View Merit List
                </button>
                
                {(profile?.role === 'SCHOOL_ADMIN' || profile?.role === 'TEACHER') && (
                  <button 
                    onClick={() => publishExam(exam.id, exam.status)}
                    className="w-full flex items-center justify-center border-2 border-slate-100 mt-4 hover:border-indigo-600 hover:text-indigo-600 text-slate-500 font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition-colors"
                  >
                    {exam.status === 'PUBLISHED' ? 'Unpublish Results' : 'Publish Results'}
                  </button>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
