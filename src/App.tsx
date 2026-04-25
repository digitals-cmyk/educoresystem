/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './lib/auth-context';
import { LoginPage } from './pages/LoginPage';
import { SuperAdminLayout } from './layouts/SuperAdminLayout';
import { SchoolLayout } from './layouts/SchoolLayout';
import { LoadingSpinner } from './components/LoadingSpinner';

const ProtectedRoute = ({ children, allowedRoles }: { children: React.ReactNode, allowedRoles?: string[] }) => {
  const { user, profile, loading } = useAuth();

  if (loading) return <LoadingSpinner />;
  
  if (!user || !profile) return <Navigate to="/login" replace />;

  if (allowedRoles && !allowedRoles.includes(profile.role!)) {
    return <div>Unauthorized access</div>;
  }

  return children;
};

export default function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          
          <Route path="/super-admin/*" element={
            <ProtectedRoute allowedRoles={['SUPER_ADMIN']}>
              <SuperAdminLayout />
            </ProtectedRoute>
          } />

          <Route path="/school/*" element={
            <ProtectedRoute allowedRoles={['SCHOOL_ADMIN', 'TEACHER', 'STUDENT', 'PARENT', 'STAFF']}>
              <SchoolLayout />
            </ProtectedRoute>
          } />

          <Route path="/" element={<Navigate to="/login" replace />} />
        </Routes>
      </Router>
    </AuthProvider>
  );
}

