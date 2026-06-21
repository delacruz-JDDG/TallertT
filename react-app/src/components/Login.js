// src/components/Login.js
import React, { useState } from 'react';

function Login() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            const response = await fetch('http://localhost/tallert/api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            });

            const data = await response.json();

            if (data.success) {
                localStorage.setItem('usuario', JSON.stringify(data.usuario));
                alert('¡Bienvenido ' + data.usuario.nombre + '!');
                window.location.href = '/dashboard';
            } else {
                setError(data.message || 'Error al iniciar sesión');
            }
        } catch (err) {
            setError('Error de conexión con el servidor');
            console.error('Error de login:', err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div style={{ 
            display: 'flex', 
            justifyContent: 'center', 
            alignItems: 'center', 
            height: '100vh',
            background: '#f0f2f5'
        }}>
            <div style={{
                background: 'white',
                padding: '40px',
                borderRadius: '10px',
                boxShadow: '0 0 20px rgba(0,0,0,0.1)',
                width: '400px'
            }}>
                <h2 style={{ textAlign: 'center', marginBottom: '5px', color: '#1a1a2e' }}>
                    <i className="fas fa-tools" style={{ color: '#4f46e5', marginRight: '10px' }}></i>
                    TallerTech
                </h2>
                <p style={{ textAlign: 'center', color: '#64748b', marginBottom: '30px' }}>
                    Sistema de Gestión de Órdenes de Servicio
                </p>

                {error && (
                    <div style={{
                        background: '#fee2e2',
                        color: '#991b1b',
                        padding: '12px',
                        borderRadius: '8px',
                        marginBottom: '20px',
                        textAlign: 'center',
                        border: '1px solid #fecaca'
                    }}>
                        <i className="fas fa-exclamation-circle" style={{ marginRight: '8px' }}></i>
                        {error}
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: '15px' }}>
                        <label style={{ display: 'block', fontWeight: '500', marginBottom: '5px', color: '#1e293b' }}>
                            Usuario
                        </label>
                        <div style={{ display: 'flex', alignItems: 'center' }}>
                            <span style={{
                                background: '#f8fafc',
                                border: '2px solid #e2e8f0',
                                borderRight: 'none',
                                padding: '10px',
                                borderRadius: '8px 0 0 8px',
                                color: '#64748b'
                            }}>
                                <i className="fas fa-user"></i>
                            </span>
                            <input
                                type="text"
                                placeholder="Ingresa tu usuario"
                                value={username}
                                onChange={(e) => setUsername(e.target.value)}
                                required
                                style={{
                                    flex: 1,
                                    padding: '10px',
                                    border: '2px solid #e2e8f0',
                                    borderLeft: 'none',
                                    borderRadius: '0 8px 8px 0',
                                    fontSize: '16px',
                                    outline: 'none'
                                }}
                            />
                        </div>
                    </div>

                    <div style={{ marginBottom: '25px' }}>
                        <label style={{ display: 'block', fontWeight: '500', marginBottom: '5px', color: '#1e293b' }}>
                            Contraseña
                        </label>
                        <div style={{ display: 'flex', alignItems: 'center' }}>
                            <span style={{
                                background: '#f8fafc',
                                border: '2px solid #e2e8f0',
                                borderRight: 'none',
                                padding: '10px',
                                borderRadius: '8px 0 0 8px',
                                color: '#64748b'
                            }}>
                                <i className="fas fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                placeholder="Ingresa tu contraseña"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                                style={{
                                    flex: 1,
                                    padding: '10px',
                                    border: '2px solid #e2e8f0',
                                    borderLeft: 'none',
                                    borderRadius: '0 8px 8px 0',
                                    fontSize: '16px',
                                    outline: 'none'
                                }}
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={loading}
                        style={{
                            width: '100%',
                            padding: '12px',
                            background: loading ? '#94a3b8' : '#4f46e5',
                            color: 'white',
                            border: 'none',
                            borderRadius: '8px',
                            fontSize: '16px',
                            fontWeight: '600',
                            cursor: loading ? 'not-allowed' : 'pointer'
                        }}
                    >
                        {loading ? (
                            <>
                                <i className="fas fa-spinner fa-spin" style={{ marginRight: '8px' }}></i>
                                Verificando...
                            </>
                        ) : (
                            <>
                                <i className="fas fa-sign-in-alt" style={{ marginRight: '8px' }}></i>
                                Iniciar Sesión
                            </>
                        )}
                    </button>
                </form>

                <p style={{
                    textAlign: 'center',
                    marginTop: '20px',
                    fontSize: '13px',
                    color: '#64748b'
                }}>
                    <i className="fas fa-key" style={{ marginRight: '5px' }}></i>
                    Credenciales: <strong>admin</strong> / <strong>admin123</strong>
                </p>
            </div>
        </div>
    );
}

export default Login;