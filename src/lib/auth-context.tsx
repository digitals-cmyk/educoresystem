import { initializeApp } from 'firebase/app';
import { getAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword, signOut, onAuthStateChanged, User, sendPasswordResetEmail } from 'firebase/auth';
import { getFirestore, doc, getDoc, setDoc } from 'firebase/firestore';
import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { auth, db } from './firebase';

export type UserRole = 'SUPER_ADMIN' | 'SCHOOL_ADMIN' | 'TEACHER' | 'STUDENT' | 'PARENT' | 'STAFF' | null;

export interface UserProfile {
  role: UserRole;
  schoolId?: string;
  name: string;
  email: string;
  status: 'ACTIVE' | 'INACTIVE';
  forcePasswordChange?: boolean;
}

interface AuthContextType {
  user: User | null;
  profile: UserProfile | null;
  loading: boolean;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const unsub = onAuthStateChanged(auth, async (firebaseUser) => {
      setUser(firebaseUser);
      if (firebaseUser) {
        // Fetch user profile
        try {
          const docRef = doc(db, 'users', firebaseUser.uid);
          const docSnap = await getDoc(docRef);
          if (docSnap.exists()) {
            setProfile(docSnap.data() as UserProfile);
          } else {
            // Seed super admin dynamically if it's the specific email
            if (firebaseUser.email === 'admin@pro.com' || firebaseUser.email === 'johnkimeujk6@gmail.com') {
              const newProfile: UserProfile = {
                role: 'SUPER_ADMIN',
                name: firebaseUser.displayName || 'Super Admin',
                email: firebaseUser.email,
                status: 'ACTIVE',
              };
              await setDoc(docRef, newProfile);
              setProfile(newProfile);
            } else {
              setProfile(null);
            }
          }
        } catch (error) {
          console.error("Error fetching user profile", error);
          setProfile(null);
        }
      } else {
        setProfile(null);
      }
      setLoading(false);
    });
    return () => unsub();
  }, []);

  const logout = async () => {
    await signOut(auth);
  };

  return (
    <AuthContext.Provider value={{ user, profile, loading, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
