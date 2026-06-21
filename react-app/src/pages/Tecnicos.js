// src/pages/Tecnicos.js
// Página de gestión de técnicos

import React, { useState, useEffect } from 'react';

function Tecnicos() {
    const [tecnicos, setTecnicos] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [showForm, setShowForm] = useState(false);
    const [editando, setEditando] = useState(null);
    const [formData, setFormData] = useState({
        nombre: '',
        especialidad: '',
        telefono: '',
        estado: 'activo'
    });
    const [erroresForm, setErroresForm] = useState({});

    const cargarTecnicos = async () => {
        setLoading(true);
        try {
            const response = await fetch('http://localhost/tallert/api/tecnicos.php?action=listar');
            const data = await response.json();
            setTecnicos(data || []);
            setError('');
        } catch (error) {
            setError('Error al cargar técnicos');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        cargarTecnicos();
    }, []);

    const validarFormulario = () => {
        const errores = {};

        if (!formData.nombre.trim()) {
            errores.nombre = 'El nombre es obligatorio';
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(formData.nombre)) {
            errores.nombre = 'El nombre solo puede contener letras y espacios';
        }

        if (!formData.especialidad.trim()) {
            errores.especialidad = 'La especialidad es obligatoria';
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
                ? `http://localhost/tallert/api/tecnicos.php?action=actualizar&id=${editando}`
                : 'http://localhost/tallert/api/tecnicos.php?action=guardar';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            if (data.success) {
                setShowForm(false);
                setEditando(null);
                setFormData({ nombre: '', especialidad: '', telefono: '', estado: 'activo' });
                setErroresForm({});
                await cargarTecnicos();
            } else {
                setError(data.error || 'Error al guardar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const eliminarTecnico = async (id, nombre) => {
        if (!window.confirm(`¿Eliminar a "${nombre}"?`)) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/tecnicos.php?action=eliminar&id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await cargarTecnicos();
            } else {
                setError(data.error || 'Error al eliminar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const editarTecnico = (tecnico) => {
        setEditando(tecnico.id_tecnico);
        setFormData({
            nombre: tecnico.nombre || '',
            especialidad: tecnico.especialidad || '',
            telefono: tecnico.telefono || '',
            estado: tecnico.estado || 'activo'
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
        pageTitleIcon: { color: '#7c3aed', marginRight: '10px' },
        pageSubtitle: { fontSize: '14px', color: '#64748b', display: 'block', marginTop: '4px' },
        btnPrimary: {
            background: '#7c3aed',
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
        badgeEstado: (estado) => ({
            padding: '4px 12px',
            borderRadius: '20px',
            fontSize: '12px',
            fontWeight: '500',
            background: estado === 'activo' ? '#d1fae5' : '#fce4ec',
            color: estado === 'activo' ? '#065f46' : '#b71c1c'
        }),
        badgeEspecialidad: {
            background: '#fef3c7',
            color: '#92400e',
            padding: '3px 12px',
            borderRadius: '20px',
            fontSize: '12px',
            fontWeight: '500'
        },
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
            background: '#7c3aed'
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

    if (loading && tecnicos.length === 0) {
        return (
            <div style={styles.loading}>
                <i className="fas fa-spinner fa-spin" style={{ fontSize: '30px', color: '#7c3aed' }}></i>
                <p>Cargando técnicos...</p>
            </div>
        );
    }

    return (
        <div style={styles.container}>
            {/* PAGE HEADER */}
            <div style={styles.pageHeader}>
                <div>
                    <h2 style={styles.pageTitle}>
                        <i className="fas fa-user-cog" style={styles.pageTitleIcon}></i>
                        Técnicos
                    </h2>
                    <p style={styles.pageSubtitle}>Gestión de técnicos del taller</p>
                </div>
                <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                    <div style={styles.searchBox}>
                        <input
                            type="text"
                            placeholder="Buscar técnico..."
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
                    <button style={styles.btnPrimary} onClick={() => { setShowForm(true); setEditando(null); setFormData({ nombre: '', especialidad: '', telefono: '', estado: 'activo' }); setErroresForm({}); }}>
                        <i className="fas fa-plus"></i> Nuevo Técnico
                    </button>
                </div>
            </div>

            {/* ERROR GLOBAL */}
            {error && <div style={styles.errorGlobal}><i className="fas fa-exclamation-circle" style={{ marginRight: '8px' }}></i>{error}</div>}

            {/* FORMULARIO */}
            {showForm && (
                <div style={styles.formContainer}>
                    <h3 style={styles.formTitle}>
                        <i className={`fas ${editando ? 'fa-edit' : 'fa-user-plus'}`} style={{ color: '#7c3aed', marginRight: '10px' }}></i>
                        {editando ? 'Editar Técnico' : 'Nuevo Técnico'}
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
                                    placeholder="Ej: Carlos Pérez"
                                    title="Solo letras y espacios"
                                />
                                {erroresForm.nombre && <span style={styles.errorMensaje}>{erroresForm.nombre}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Especialidad *</label>
                                <select
                                    style={styles.select}
                                    value={formData.especialidad}
                                    onChange={(e) => setFormData({ ...formData, especialidad: e.target.value })}
                                >
                                    <option value="">Seleccionar especialidad...</option>
                                    <option value="Celulares/Tablets">Celulares/Tablets</option>
                                    <option value="Computadores">Computadores</option>
                                    <option value="Electrodomésticos">Electrodomésticos</option>
                                    <option value="TV/Audio">TV/Audio</option>
                                    <option value="Redes">Redes</option>
                                    <option value="General">General</option>
                                </select>
                                {erroresForm.especialidad && <span style={styles.errorMensaje}>{erroresForm.especialidad}</span>}
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
                                <label style={styles.label}>Estado</label>
                                <select
                                    style={styles.select}
                                    value={formData.estado}
                                    onChange={(e) => setFormData({ ...formData, estado: e.target.value })}
                                >
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
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

            {/* ===== TABLA ===== */}
            <div style={styles.tableContainer}>
                <table style={styles.table}>
                    <thead>
                        <tr>
                            <th style={styles.th}>#</th>
                            <th style={styles.th}>Técnico</th>
                            <th style={styles.th}>Especialidad</th>
                            <th style={styles.th}>Teléfono</th>
                            <th style={styles.th}>Estado</th>
                            <th style={styles.th}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {tecnicos.length === 0 ? (
                            <tr>
                                <td colSpan="6" style={{ ...styles.td, textAlign: 'center', padding: '40px', color: '#94a3b8' }}>
                                    <i className="fas fa-user-cog" style={{ fontSize: '24px', display: 'block', marginBottom: '10px', opacity: '0.3' }}></i>
                                    No hay técnicos registrados
                                </td>
                            </tr>
                        ) : (
                            tecnicos.map((tecnico) => (
                                <tr key={tecnico.id_tecnico}>
                                    <td style={styles.td}>
                                        <span style={styles.avatar}>
                                            {tecnico.nombre ? tecnico.nombre.charAt(0).toUpperCase() : '?'}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <strong>{tecnico.nombre}</strong>
                                    </td>
                                    <td style={styles.td}>
                                        <span style={styles.badgeEspecialidad}>
                                            <i className="fas fa-tools" style={{ marginRight: '4px' }}></i>
                                            {tecnico.especialidad || 'Sin especialidad'}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <i className="fas fa-phone" style={{ color: '#94a3b8', marginRight: '6px', fontSize: '12px' }}></i>
                                        {tecnico.telefono}
                                    </td>
                                    <td style={styles.td}>
                                        <span style={styles.badgeEstado(tecnico.estado)}>
                                            {tecnico.estado === 'activo' ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <button style={styles.btnInfo} onClick={() => alert('Ver técnico: ' + tecnico.nombre)} title="Ver">
                                            <i className="fas fa-eye"></i> Ver
                                        </button>
                                        <button style={styles.btnSuccess} onClick={() => editarTecnico(tecnico)} title="Editar">
                                            <i className="fas fa-pen"></i> Editar
                                        </button>
                                        <button style={styles.btnDanger} onClick={() => eliminarTecnico(tecnico.id_tecnico, tecnico.nombre)} title="Eliminar">
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

export default Tecnicos;