// src/pages/Equipos.js
// Página de gestión de equipos

import React, { useState, useEffect } from 'react';

function Equipos() {
    const [equipos, setEquipos] = useState([]);
    const [clientes, setClientes] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [showForm, setShowForm] = useState(false);
    const [editando, setEditando] = useState(null);
    const [formData, setFormData] = useState({
        id_cliente: '',
        marca: '',
        modelo: '',
        serial: '',
        tipo: 'computador'
    });
    const [erroresForm, setErroresForm] = useState({});

    const cargarEquipos = async () => {
        setLoading(true);
        try {
            const response = await fetch('http://localhost/tallert/api/equipos.php?action=listar');
            const data = await response.json();
            setEquipos(data || []);
            setError('');
        } catch (error) {
            setError('Error al cargar equipos');
        } finally {
            setLoading(false);
        }
    };

    const cargarClientes = async () => {
        try {
            const response = await fetch('http://localhost/tallert/api/clientes.php?action=listar');
            const data = await response.json();
            setClientes(data || []);
        } catch (error) {
            console.error('Error al cargar clientes:', error);
        }
    };

    useEffect(() => {
        cargarEquipos();
        cargarClientes();
    }, []);

    const validarFormulario = () => {
        const errores = {};

        if (!formData.id_cliente) {
            errores.id_cliente = 'Debe seleccionar un cliente';
        }

        if (!formData.marca.trim()) {
            errores.marca = 'La marca es obligatoria';
        } else if (formData.marca.length < 2) {
            errores.marca = 'La marca debe tener al menos 2 caracteres';
        }

        if (!formData.modelo.trim()) {
            errores.modelo = 'El modelo es obligatorio';
        } else if (formData.modelo.length < 2) {
            errores.modelo = 'El modelo debe tener al menos 2 caracteres';
        }

        if (!formData.serial.trim()) {
            errores.serial = 'El número de serie es obligatorio';
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
                ? `http://localhost/tallert/api/equipos.php?action=actualizar&id=${editando}`
                : 'http://localhost/tallert/api/equipos.php?action=guardar';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            if (data.success) {
                setShowForm(false);
                setEditando(null);
                setFormData({ id_cliente: '', marca: '', modelo: '', serial: '', tipo: 'computador' });
                setErroresForm({});
                await cargarEquipos();
            } else {
                setError(data.error || 'Error al guardar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const eliminarEquipo = async (id, nombre) => {
        if (!window.confirm(`¿Eliminar el equipo "${nombre}"?`)) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/equipos.php?action=eliminar&id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await cargarEquipos();
            } else {
                setError(data.error || 'Error al eliminar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const editarEquipo = (equipo) => {
        setEditando(equipo.id_equipo);
        setFormData({
            id_cliente: equipo.id_cliente || '',
            marca: equipo.marca || '',
            modelo: equipo.modelo || '',
            serial: equipo.serial || '',
            tipo: equipo.tipo || 'computador'
        });
        setErroresForm({});
        setShowForm(true);
    };

    const getIconoTipo = (tipo) => {
        const iconos = {
            computador: 'fa-laptop',
            celular: 'fa-mobile-alt',
            tablet: 'fa-tablet-alt',
            electrodomestico: 'fa-tv',
            otro: 'fa-microchip'
        };
        return iconos[tipo] || 'fa-microchip';
    };

    const getColorTipo = (tipo) => {
        const colores = {
            computador: '#3b82f6',
            celular: '#10b981',
            tablet: '#f59e0b',
            electrodomestico: '#ef4444',
            otro: '#8b5cf6'
        };
        return colores[tipo] || '#8b5cf6';
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
        pageTitleIcon: { color: '#3b82f6', marginRight: '10px' },
        pageSubtitle: { fontSize: '14px', color: '#64748b', display: 'block', marginTop: '4px' },
        btnPrimary: {
            background: '#3b82f6',
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
        badgeTipo: (tipo) => ({
            padding: '3px 12px',
            borderRadius: '20px',
            fontSize: '12px',
            fontWeight: '500',
            background: tipo === 'computador' ? '#dbeafe' : 
                       tipo === 'celular' ? '#d1fae5' : 
                       tipo === 'tablet' ? '#fef3c7' : 
                       tipo === 'electrodomestico' ? '#fce4ec' : '#ede9fe',
            color: tipo === 'computador' ? '#1e40af' : 
                   tipo === 'celular' ? '#065f46' : 
                   tipo === 'tablet' ? '#92400e' : 
                   tipo === 'electrodomestico' ? '#b71c1c' : '#5b21b6'
        }),
        equipoIcon: (tipo) => ({
            width: '40px',
            height: '40px',
            borderRadius: '10px',
            display: 'inline-flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: '18px',
            background: tipo === 'computador' ? '#dbeafe' : 
                       tipo === 'celular' ? '#d1fae5' : 
                       tipo === 'tablet' ? '#fef3c7' : 
                       tipo === 'electrodomestico' ? '#fce4ec' : '#ede9fe',
            color: tipo === 'computador' ? '#1e40af' : 
                   tipo === 'celular' ? '#065f46' : 
                   tipo === 'tablet' ? '#92400e' : 
                   tipo === 'electrodomestico' ? '#b71c1c' : '#5b21b6'
        }),
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

    if (loading && equipos.length === 0) {
        return (
            <div style={styles.loading}>
                <i className="fas fa-spinner fa-spin" style={{ fontSize: '30px', color: '#3b82f6' }}></i>
                <p>Cargando equipos...</p>
            </div>
        );
    }

    return (
        <div style={styles.container}>
            {/* PAGE HEADER */}
            <div style={styles.pageHeader}>
                <div>
                    <h2 style={styles.pageTitle}>
                        <i className="fas fa-desktop" style={styles.pageTitleIcon}></i>
                        Equipos
                    </h2>
                    <p style={styles.pageSubtitle}>Gestión de equipos de los clientes</p>
                </div>
                <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                    <div style={styles.searchBox}>
                        <input
                            type="text"
                            placeholder="Buscar equipo..."
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
                    <button style={styles.btnPrimary} onClick={() => { setShowForm(true); setEditando(null); setFormData({ id_cliente: '', marca: '', modelo: '', serial: '', tipo: 'computador' }); setErroresForm({}); }}>
                        <i className="fas fa-plus"></i> Nuevo Equipo
                    </button>
                </div>
            </div>

            {/* ERROR GLOBAL */}
            {error && <div style={styles.errorGlobal}><i className="fas fa-exclamation-circle" style={{ marginRight: '8px' }}></i>{error}</div>}

            {/* FORMULARIO */}
            {showForm && (
                <div style={styles.formContainer}>
                    <h3 style={styles.formTitle}>
                        <i className={`fas ${editando ? 'fa-edit' : 'fa-plus-circle'}`} style={{ color: '#3b82f6', marginRight: '10px' }}></i>
                        {editando ? 'Editar Equipo' : 'Nuevo Equipo'}
                    </h3>
                    <form onSubmit={handleSubmit}>
                        <div style={styles.formGrid}>
                            <div style={styles.formGroupFull}>
                                <label style={styles.label}>Cliente propietario *</label>
                                <select
                                    style={{ ...styles.select, ...(erroresForm.id_cliente ? styles.inputError : {}) }}
                                    value={formData.id_cliente}
                                    onChange={(e) => setFormData({ ...formData, id_cliente: e.target.value })}
                                >
                                    <option value="">Seleccionar cliente...</option>
                                    {clientes.map((cliente) => (
                                        <option key={cliente.id_cliente} value={cliente.id_cliente}>
                                            {cliente.nombre} - {cliente.email}
                                        </option>
                                    ))}
                                </select>
                                {erroresForm.id_cliente && <span style={styles.errorMensaje}>{erroresForm.id_cliente}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Marca *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.marca ? styles.inputError : {}) }}
                                    type="text"
                                    required
                                    value={formData.marca}
                                    onChange={(e) => setFormData({ ...formData, marca: e.target.value })}
                                    placeholder="Ej: Samsung"
                                />
                                {erroresForm.marca && <span style={styles.errorMensaje}>{erroresForm.marca}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Modelo *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.modelo ? styles.inputError : {}) }}
                                    type="text"
                                    required
                                    value={formData.modelo}
                                    onChange={(e) => setFormData({ ...formData, modelo: e.target.value })}
                                    placeholder="Ej: Galaxy S23"
                                />
                                {erroresForm.modelo && <span style={styles.errorMensaje}>{erroresForm.modelo}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Número de Serie *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.serial ? styles.inputError : {}) }}
                                    type="text"
                                    required
                                    value={formData.serial}
                                    onChange={(e) => setFormData({ ...formData, serial: e.target.value })}
                                    placeholder="Ej: SN-XXX-001"
                                />
                                {erroresForm.serial && <span style={styles.errorMensaje}>{erroresForm.serial}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Tipo de equipo *</label>
                                <select
                                    style={{ ...styles.select, ...(erroresForm.tipo ? styles.inputError : {}) }}
                                    value={formData.tipo}
                                    onChange={(e) => setFormData({ ...formData, tipo: e.target.value })}
                                >
                                    <option value="computador">Computador</option>
                                    <option value="celular">Celular</option>
                                    <option value="tablet">Tablet</option>
                                    <option value="electrodomestico">Electrodoméstico</option>
                                    <option value="otro">Otro</option>
                                </select>
                                {erroresForm.tipo && <span style={styles.errorMensaje}>{erroresForm.tipo}</span>}
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
                            <th style={styles.th}>Equipo</th>
                            <th style={styles.th}>Serial</th>
                            <th style={styles.th}>Cliente</th>
                            <th style={styles.th}>Tipo</th>
                            <th style={styles.th}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {equipos.length === 0 ? (
                            <tr>
                                <td colSpan="6" style={{ ...styles.td, textAlign: 'center', padding: '40px', color: '#94a3b8' }}>
                                    <i className="fas fa-desktop" style={{ fontSize: '24px', display: 'block', marginBottom: '10px', opacity: '0.3' }}></i>
                                    No hay equipos registrados
                                </td>
                            </tr>
                        ) : (
                            equipos.map((equipo) => (
                                <tr key={equipo.id_equipo}>
                                    <td style={styles.td}>
                                        <span style={styles.equipoIcon(equipo.tipo)}>
                                            <i className={`fas ${getIconoTipo(equipo.tipo)}`}></i>
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <strong>{equipo.marca} {equipo.modelo}</strong>
                                    </td>
                                    <td style={styles.td}>
                                        <code style={{ fontSize: '12px', background: '#f1f5f9', padding: '2px 8px', borderRadius: '4px' }}>
                                            {equipo.serial}
                                        </code>
                                    </td>
                                    <td style={styles.td}>
                                        <a href="#" style={{ color: '#4f46e5', textDecoration: 'none' }}>
                                            {equipo.cliente_nombre || 'N/A'}
                                        </a>
                                    </td>
                                    <td style={styles.td}>
                                        <span style={styles.badgeTipo(equipo.tipo)}>
                                            {equipo.tipo || 'Otro'}
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <button style={styles.btnInfo} onClick={() => alert('Ver equipo: ' + equipo.marca + ' ' + equipo.modelo)} title="Ver">
                                            <i className="fas fa-eye"></i> Ver
                                        </button>
                                        <button style={styles.btnSuccess} onClick={() => editarEquipo(equipo)} title="Editar">
                                            <i className="fas fa-pen"></i> Editar
                                        </button>
                                        <button style={styles.btnDanger} onClick={() => eliminarEquipo(equipo.id_equipo, equipo.marca + ' ' + equipo.modelo)} title="Eliminar">
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

export default Equipos;