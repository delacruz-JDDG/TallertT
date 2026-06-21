// src/pages/Clientes.js
// Página de gestión de clientes con botones modernos

import React, { useState, useEffect } from 'react';

function Clientes() {
    const [clientes, setClientes] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [showForm, setShowForm] = useState(false);
    const [editando, setEditando] = useState(null);
    const [formData, setFormData] = useState({
        nombre: '',
        email: '',
        telefono: '',
        direccion: '',
        tipo: 'particular'
    });
    const [erroresForm, setErroresForm] = useState({});

    const cargarClientes = async () => {
        setLoading(true);
        try {
            const response = await fetch('http://localhost/tallert/api/clientes.php?action=listar');
            const data = await response.json();
            setClientes(data || []);
            setError('');
        } catch (error) {
            setError('Error al cargar clientes');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        cargarClientes();
    }, []);

    const validarFormulario = () => {
        const errores = {};

        if (!formData.nombre.trim()) {
            errores.nombre = 'El nombre es obligatorio';
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(formData.nombre)) {
            errores.nombre = 'El nombre solo puede contener letras y espacios';
        }

        if (!formData.email.trim()) {
            errores.email = 'El email es obligatorio';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
            errores.email = 'Ingrese un email válido';
        }

        if (!formData.telefono.trim()) {
            errores.telefono = 'El teléfono es obligatorio';
        } else if (!/^[0-9]+$/.test(formData.telefono)) {
            errores.telefono = 'El teléfono solo debe contener números';
        } else if (formData.telefono.length > 15) {
            errores.telefono = 'El teléfono no puede tener más de 15 dígitos';
        }

        setErroresForm(errores);
        return Object.keys(errores).length === 0;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }

        try {
            const url = editando 
                ? `http://localhost/tallert/api/clientes.php?action=actualizar&id=${editando}`
                : 'http://localhost/tallert/api/clientes.php?action=guardar';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            if (data.success) {
                setShowForm(false);
                setEditando(null);
                setFormData({ nombre: '', email: '', telefono: '', direccion: '', tipo: 'particular' });
                setErroresForm({});
                await cargarClientes();
            } else {
                setError(data.error || 'Error al guardar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const eliminarCliente = async (id, nombre) => {
        if (!window.confirm(`¿Eliminar a "${nombre}"?`)) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/clientes.php?action=eliminar&id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await cargarClientes();
            } else {
                setError(data.error || 'Error al eliminar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const editarCliente = (cliente) => {
        setEditando(cliente.id_cliente);
        setFormData({
            nombre: cliente.nombre || '',
            email: cliente.email || '',
            telefono: cliente.telefono || '',
            direccion: cliente.direccion || '',
            tipo: cliente.tipo || 'particular'
        });
        setErroresForm({});
        setShowForm(true);
    };

    const handleInputChange = (campo, valor) => {
        if (campo === 'nombre') {
            const soloLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
            if (!soloLetras.test(valor) && valor !== '') return;
        }
        if (campo === 'telefono') {
            const soloNumeros = /^[0-9]*$/;
            if (!soloNumeros.test(valor) && valor !== '') return;
            if (valor.length > 15) return;
        }
        setFormData({ ...formData, [campo]: valor });
        if (erroresForm[campo]) {
            setErroresForm({ ...erroresForm, [campo]: '' });
        }
    };

    // ===== ESTILOS =====
    const styles = {
        container: { padding: '20px 0' },
        pageHeader: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            marginBottom: '25px',
            flexWrap: 'wrap',
            gap: '10px'
        },
        pageTitle: {
            fontWeight: '700',
            color: '#1e293b',
            fontSize: '24px'
        },
        pageTitleIcon: { color: '#4f46e5', marginRight: '10px' },
        pageSubtitle: { fontSize: '14px', color: '#64748b', display: 'block', marginTop: '4px' },
        btnPrimary: {
            background: '#4f46e5',
            color: 'white',
            border: 'none',
            padding: '10px 20px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '14px',
            fontWeight: '500',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '8px',
            transition: 'background 0.2s'
        },
        // ===== BOTONES MODERNOS =====
        btnSuccess: {
            background: '#10b981',
            color: 'white',
            border: 'none',
            padding: '8px 16px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '13px',
            marginRight: '6px',
            transition: 'background 0.2s',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '6px'
        },
        btnDanger: {
            background: '#ef4444',
            color: 'white',
            border: 'none',
            padding: '8px 16px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '13px',
            transition: 'background 0.2s',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '6px'
        },
        btnInfo: {
            background: '#3b82f6',
            color: 'white',
            border: 'none',
            padding: '8px 16px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '13px',
            marginRight: '6px',
            transition: 'background 0.2s',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '6px'
        },
        btnSecondary: {
            background: '#64748b',
            color: 'white',
            border: 'none',
            padding: '10px 20px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '14px',
            marginLeft: '10px',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '6px',
            transition: 'background 0.2s'
        },
        tableContainer: {
            background: 'white',
            borderRadius: '12px',
            border: '1px solid #e2e8f0',
            overflow: 'hidden'
        },
        table: { width: '100%', borderCollapse: 'collapse', fontSize: '14px' },
        th: {
            background: '#f8fafc',
            padding: '12px 16px',
            textAlign: 'left',
            borderBottom: '2px solid #e2e8f0',
            fontWeight: '600',
            color: '#475569',
            textTransform: 'uppercase',
            fontSize: '12px',
            letterSpacing: '0.5px'
        },
        td: { padding: '12px 16px', borderBottom: '1px solid #f1f5f9', verticalAlign: 'middle' },
        badge: (tipo) => ({
            padding: '4px 12px',
            borderRadius: '20px',
            fontSize: '12px',
            fontWeight: '500',
            background: tipo === 'empresa' ? '#fef3c7' : '#dbeafe',
            color: tipo === 'empresa' ? '#92400e' : '#1e40af'
        }),
        avatar: {
            width: '35px',
            height: '35px',
            borderRadius: '50%',
            display: 'inline-flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontWeight: '600',
            fontSize: '14px',
            color: 'white',
            background: '#4f46e5'
        },
        formContainer: {
            background: 'white',
            padding: '25px',
            borderRadius: '12px',
            border: '1px solid #e2e8f0',
            marginBottom: '20px'
        },
        formTitle: { fontSize: '18px', fontWeight: '600', color: '#1e293b', marginBottom: '20px' },
        formGrid: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px' },
        formGroup: { marginBottom: '0' },
        formGroupFull: { gridColumn: '1 / -1', marginBottom: '0' },
        label: { display: 'block', fontWeight: '500', color: '#1e293b', marginBottom: '5px', fontSize: '14px' },
        input: {
            width: '100%',
            padding: '10px 14px',
            border: '2px solid #e2e8f0',
            borderRadius: '8px',
            fontSize: '14px',
            transition: 'border-color 0.2s',
            outline: 'none'
        },
        inputError: { borderColor: '#ef4444' },
        select: {
            width: '100%',
            padding: '10px 14px',
            border: '2px solid #e2e8f0',
            borderRadius: '8px',
            fontSize: '14px',
            outline: 'none',
            background: 'white'
        },
        errorMensaje: { color: '#ef4444', fontSize: '12px', marginTop: '4px', display: 'block' },
        errorGlobal: {
            background: '#fee2e2',
            color: '#991b1b',
            padding: '12px 16px',
            borderRadius: '8px',
            marginBottom: '15px',
            border: '1px solid #fecaca'
        },
        loading: { textAlign: 'center', padding: '40px', color: '#64748b' },
        searchBox: { maxWidth: '300px' }
    };

    if (loading && clientes.length === 0) {
        return (
            <div style={styles.loading}>
                <i className="fas fa-spinner fa-spin" style={{ fontSize: '30px', color: '#4f46e5' }}></i>
                <p>Cargando clientes...</p>
            </div>
        );
    }

    return (
        <div style={styles.container}>
            {/* PAGE HEADER */}
            <div style={styles.pageHeader}>
                <div>
                    <h2 style={styles.pageTitle}>
                        <i className="fas fa-users" style={styles.pageTitleIcon}></i>
                        Clientes
                    </h2>
                    <p style={styles.pageSubtitle}>Gestión de clientes del taller</p>
                </div>
                <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                    <div style={styles.searchBox}>
                        <input
                            type="text"
                            placeholder="Buscar cliente..."
                            style={{
                                width: '100%',
                                padding: '8px 12px',
                                border: '2px solid #e2e8f0',
                                borderRadius: '8px',
                                fontSize: '14px',
                                outline: 'none'
                            }}
                        />
                    </div>
                    <button style={styles.btnPrimary} onClick={() => { setShowForm(true); setEditando(null); setFormData({ nombre: '', email: '', telefono: '', direccion: '', tipo: 'particular' }); setErroresForm({}); }}>
                        <i className="fas fa-plus"></i> Nuevo Cliente
                    </button>
                </div>
            </div>

            {/* ERROR GLOBAL */}
            {error && <div style={styles.errorGlobal}><i className="fas fa-exclamation-circle" style={{ marginRight: '8px' }}></i>{error}</div>}

            {/* FORMULARIO */}
            {showForm && (
                <div style={styles.formContainer}>
                    <h3 style={styles.formTitle}>
                        <i className={`fas ${editando ? 'fa-edit' : 'fa-user-plus'}`} style={{ color: '#4f46e5', marginRight: '10px' }}></i>
                        {editando ? 'Editar Cliente' : 'Nuevo Cliente'}
                    </h3>
                    <form onSubmit={handleSubmit}>
                        <div style={styles.formGrid}>
                            <div style={styles.formGroup}>
                                <label style={styles.label}>Nombre completo *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.nombre ? styles.inputError : {}) }}
                                    type="text"
                                    required
                                    value={formData.nombre}
                                    onChange={(e) => handleInputChange('nombre', e.target.value)}
                                    placeholder="Ej: Juan Pérez"
                                    title="Solo letras y espacios"
                                />
                                {erroresForm.nombre && <span style={styles.errorMensaje}>{erroresForm.nombre}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Email *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.email ? styles.inputError : {}) }}
                                    type="email"
                                    required
                                    value={formData.email}
                                    onChange={(e) => handleInputChange('email', e.target.value)}
                                    placeholder="cliente@email.com"
                                />
                                {erroresForm.email && <span style={styles.errorMensaje}>{erroresForm.email}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Teléfono *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.telefono ? styles.inputError : {}) }}
                                    type="text"
                                    required
                                    value={formData.telefono}
                                    onChange={(e) => handleInputChange('telefono', e.target.value)}
                                    placeholder="3001234567"
                                    maxLength="15"
                                    title="Solo números, máximo 15 dígitos"
                                />
                                {erroresForm.telefono && <span style={styles.errorMensaje}>{erroresForm.telefono}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Tipo</label>
                                <select
                                    style={styles.select}
                                    value={formData.tipo}
                                    onChange={(e) => setFormData({ ...formData, tipo: e.target.value })}
                                >
                                    <option value="particular">Particular</option>
                                    <option value="empresa">Empresa</option>
                                </select>
                            </div>

                            <div style={styles.formGroupFull}>
                                <label style={styles.label}>Dirección</label>
                                <input
                                    style={styles.input}
                                    type="text"
                                    value={formData.direccion}
                                    onChange={(e) => setFormData({ ...formData, direccion: e.target.value })}
                                    placeholder="Dirección del cliente"
                                />
                            </div>
                        </div>
                        <div style={{ display: 'flex', gap: '10px', marginTop: '20px' }}>
                            <button type="submit" style={styles.btnPrimary}>
                                <i className={`fas ${editando ? 'fa-save' : 'fa-plus'}`}></i>
                                {editando ? 'Actualizar' : 'Guardar'}
                            </button>
                            <button type="button" style={styles.btnSecondary} onClick={() => { setShowForm(false); setEditando(null); setErroresForm({}); }}>
                                <i className="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            )}

            {/* ===== TABLA CON BOTONES MODERNOS ===== */}
            <div style={styles.tableContainer}>
                <table style={styles.table}>
                    <thead>
                        <tr>
                            <th style={styles.th}>#</th>
                            <th style={styles.th}>Cliente</th>
                            <th style={styles.th}>Contacto</th>
                            <th style={styles.th}>Tipo</th>
                            <th style={styles.th}>Equipos</th>
                            <th style={styles.th}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {clientes.length === 0 ? (
                            <tr>
                                <td colSpan="6" style={{ ...styles.td, textAlign: 'center', padding: '40px', color: '#94a3b8' }}>
                                    <i className="fas fa-users" style={{ fontSize: '24px', display: 'block', marginBottom: '10px', opacity: '0.3' }}></i>
                                    No hay clientes registrados
                                </td>
                            </tr>
                        ) : (
                            clientes.map((cliente) => (
                                <tr key={cliente.id_cliente}>
                                    <td style={styles.td}>
                                        <span style={styles.avatar}>
                                            {cliente.nombre ? cliente.nombre.charAt(0).toUpperCase() : '?'}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <strong>{cliente.nombre}</strong>
                                        <div style={{ fontSize: '13px', color: '#64748b' }}>
                                            {cliente.direccion || 'Sin dirección'}
                                        </div>
                                    </td>
                                    <td style={styles.td}>
                                        <div><i className="fas fa-envelope" style={{ color: '#94a3b8', marginRight: '6px', fontSize: '12px' }}></i> {cliente.email}</div>
                                        <div><i className="fas fa-phone" style={{ color: '#94a3b8', marginRight: '6px', fontSize: '12px' }}></i> {cliente.telefono}</div>
                                    </td>
                                    <td style={styles.td}>
                                        <span style={styles.badge(cliente.tipo)}>
                                            {cliente.tipo === 'empresa' ? 'Empresa' : 'Particular'}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <span style={{
                                            background: '#e2e8f0',
                                            color: '#475569',
                                            padding: '2px 10px',
                                            borderRadius: '20px',
                                            fontSize: '12px',
                                            fontWeight: '600'
                                        }}>
                                            {cliente.total_equipos || 0}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <button style={styles.btnInfo} onClick={() => alert('Ver cliente: ' + cliente.nombre)} title="Ver">
                                            <i className="fas fa-eye"></i> Ver
                                        </button>
                                        <button style={styles.btnSuccess} onClick={() => editarCliente(cliente)} title="Editar">
                                            <i className="fas fa-pen"></i> Editar
                                        </button>
                                        <button style={styles.btnDanger} onClick={() => eliminarCliente(cliente.id_cliente, cliente.nombre)} title="Eliminar">
                                            <i className="fas fa-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default Clientes;