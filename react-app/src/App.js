// src/App.js
import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import Login from './components/Login';
import Dashboard from './components/Dashboard';
import Layout from './components/Layout';
import Clientes from './pages/Clientes';
import Tecnicos from './pages/Tecnicos';
import Equipos from './pages/Equipos';
import Repuestos from './pages/Repuestos';
import Ordenes from './pages/Ordenes';
import Reportes from './pages/Reportes';
import './App.css';

function PaginaPrueba({ titulo }) {
    return (
        <Layout>
            <h1 style={{ fontSize: '28px', fontWeight: '700', color: '#1e293b' }}>
                <i className="fas fa-arrow-left" style={{ marginRight: '15px', color: '#4f46e5' }}></i>
                {titulo}
            </h1>
            <p style={{ color: '#64748b', marginTop: '20px' }}>
                Página en construcción. Aquí irá el módulo de {titulo}.
            </p>
        </Layout>
    );
}

function RutaProtegida({ children }) {
    const usuario = localStorage.getItem('usuario');
    if (!usuario) {
        return <Navigate to="/" replace />;
    }
    return children;
}

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<Login />} />
                <Route path="/login" element={<Login />} />

                <Route path="/dashboard" element={
                    <RutaProtegida>
                        <Layout>
                            <Dashboard />
                        </Layout>
                    </RutaProtegida>
                } />

                <Route path="/clientes" element={
                      <RutaProtegida>
                          <Layout>
                              <Clientes />
                          </Layout>
                      </RutaProtegida>
                  } />

                <Route path="/tecnicos" element={
                    <RutaProtegida>
                        <Layout>
                            <Tecnicos />
                        </Layout>
                    </RutaProtegida>
                } />

                <Route path="/equipos" element={
                    <RutaProtegida>
                        <Layout>
                            <Equipos />
                        </Layout>
                    </RutaProtegida>
                } />

                <Route path="/repuestos" element={
                    <RutaProtegida>
                        <Layout>
                            <Repuestos />
                        </Layout>
                    </RutaProtegida>
                } />

                <Route path="/ordenes" element={
                    <RutaProtegida>
                        <Layout>
                            <Ordenes />
                        </Layout>
                    </RutaProtegida>
                } />

                <Route path="/reportes" element={
                    <RutaProtegida>
                        <Layout>
                            <Reportes />
                        </Layout>
                    </RutaProtegida>
                } />

                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    );
}

export default App;