// src/pages/Repuestos.js
// Página de gestión de repuestos

import React, { useState, useEffect } from 'react';

function Repuestos() {
    const [repuestos, setRepuestos] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [showForm, setShowForm] = useState(false);
    const [editando, setEditando] = useState(null);
    const [formData, setFormData] = useState({
        nombre: '',
        precio_unitario: '',
        stock: ''
    });
    const [erroresForm, setErroresForm] = useState({});

    const cargarRepuestos = async () => {
        setLoading(true);
        try {
            const response = await fetch('http://localhost/tallert/api/repuestos.php?action=listar');
            const data = await response.json();
            setRepuestos(data || []);
            setError('');
        } catch (error) {
            setError('Error al cargar repuestos');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        cargarRepuestos();
    }, []);

    const validarFormulario = () => {
        const errores = {};

        if (!formData.nombre.trim()) {
            errores.nombre = 'El nombre del repuesto es obligatorio';
        } else if (formData.nombre.length < 3) {
            errores.nombre = 'El nombre debe tener al menos 3 caracteres';
        }

        const precio = parseFloat(formData.precio_unitario.replace(/\./g, '').replace(',', '.'));
        if (!formData.precio_unitario) {
            errores.precio_unitario = 'El precio es obligatorio';
        } else if (isNaN(precio) || precio <= 0) {
            errores.precio_unitario = 'El precio debe ser mayor a 0';
        }

        const stock = parseInt(formData.stock);
        if (formData.stock === '') {
            errores.stock = 'El stock es obligatorio';
        } else if (isNaN(stock) || stock < 0) {
            errores.stock = 'El stock no puede ser negativo';
        }

        setErroresForm(errores);
        return Object.keys(errores).length === 0;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }

        const dataEnviar = {
            nombre: formData.nombre,
            precio_unitario: parseFloat(formData.precio_unitario.replace(/\./g, '').replace(',', '.')),
            stock: parseInt(formData.stock)
        };

        try {
            const url = editando 
                ? `http://localhost/tallert/api/repuestos.php?action=actualizar&id=${editando}`
                : 'http://localhost/tallert/api/repuestos.php?action=guardar';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataEnviar)
            });
            
            const data = await response.json();
            if (data.success) {
                setShowForm(false);
                setEditando(null);
                setFormData({ nombre: '', precio_unitario: '', stock: '' });
                setErroresForm({});
                await cargarRepuestos();
            } else {
                setError(data.error || 'Error al guardar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const eliminarRepuesto = async (id, nombre) => {
        if (!window.confirm(`¿Eliminar el repuesto "${nombre}"?`)) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/repuestos.php?action=eliminar&id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await cargarRepuestos();
            } else {
                setError(data.error || 'Error al eliminar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const editarRepuesto = (repuesto) => {
        setEditando(repuesto.id_repuesto);
        setFormData({
            nombre: repuesto.nombre || '',
            precio_unitario: repuesto.precio_unitario ? repuesto.precio_unitario.toLocaleString() : '',
            stock: repuesto.stock || ''
        });
        setErroresForm({});
        setShowForm(true);
    };

    const formatearPrecio = (valor) => {
        if (!valor) return '';
        const num = parseFloat(valor.toString().replace(/\./g, '').replace(',', '.'));
        if (isNaN(num)) return '';
        return num.toLocaleString();
    };

    const handlePrecioChange = (e) => {
        const valor = e.target.value.replace(/[^0-9]/g, '');
        if (valor) {
            setFormData({ ...formData, precio_unitario: parseInt(valor).toLocaleString() });
        } else {
            setFormData({ ...formData, precio_unitario: '' });
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
        stockBadge: (stock) => {
            let color, bg;
            if (stock > 20) { color = '#065f46'; bg = '#d1fae5'; }
            else if (stock > 5) { color = '#92400e'; bg = '#fef3c7'; }
            else if (stock > 0) { color = '#b71c1c'; bg = '#fce4ec'; }
            else { color = '#991b1b'; bg = '#fee2e2'; }
            return { padding: '4px 12px', borderRadius: '20px', fontSize: '12px', fontWeight: '600', background: bg, color: color };
        },
        repuestoIcon: {
            width: '35px',
            height: '35px',
            borderRadius: '8px',
            display: 'inline-flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: '16px',
            background: '#ede9fe',
            color: '#7c3aed'
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

    if (loading && repuestos.length === 0) {
        return (
            <div style={styles.loading}>
                <i className="fas fa-spinner fa-spin" style={{ fontSize: '30px', color: '#7c3aed' }}></i>
                <p>Cargando repuestos...</p>
            </div>
        );
    }

    return (
        <div style={styles.container}>
            {/* PAGE HEADER */}
            <div style={styles.pageHeader}>
                <div>
                    <h2 style={styles.pageTitle}>
                        <i className="fas fa-microchip" style={styles.pageTitleIcon}></i>
                        Repuestos
                    </h2>
                    <p style={styles.pageSubtitle}>Gestión de inventario de repuestos</p>
                </div>
                <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                    <div style={styles.searchBox}>
                        <input
                            type="text"
                            placeholder="Buscar repuesto..."
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
                    <button style={styles.btnPrimary} onClick={() => { setShowForm(true); setEditando(null); setFormData({ nombre: '', precio_unitario: '', stock: '' }); setErroresForm({}); }}>
                        <i className="fas fa-plus"></i> Nuevo Repuesto
                    </button>
                </div>
            </div>

            {/* ERROR GLOBAL */}
            {error && <div style={styles.errorGlobal}><i className="fas fa-exclamation-circle" style={{ marginRight: '8px' }}></i>{error}</div>}

            {/* FORMULARIO */}
            {showForm && (
                <div style={styles.formContainer}>
                    <h3 style={styles.formTitle}>
                        <i className={`fas ${editando ? 'fa-edit' : 'fa-plus-circle'}`} style={{ color: '#7c3aed', marginRight: '10px' }}></i>
                        {editando ? 'Editar Repuesto' : 'Nuevo Repuesto'}
                    </h3>
                    <form onSubmit={handleSubmit}>
                        <div style={styles.formGrid}>
                            <div style={styles.formGroupFull}>
                                <label style={styles.label}>Nombre del repuesto *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.nombre ? styles.inputError : {}) }}
                                    type="text"
                                    required
                                    value={formData.nombre}
                                    onChange={(e) => setFormData({ ...formData, nombre: e.target.value })}
                                    placeholder="Ej: Batería iPhone 13"
                                />
                                {erroresForm.nombre && <span style={styles.errorMensaje}>{erroresForm.nombre}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Precio Unitario *</label>
                                <div style={{ display: 'flex', alignItems: 'center' }}>
                                    <span style={{ background: '#f8fafc', border: '2px solid #e2e8f0', borderRight: 'none', padding: '10px 14px', borderRadius: '8px 0 0 8px' }}>$</span>
                                    <input
                                        style={{ ...styles.input, ...(erroresForm.precio_unitario ? styles.inputError : {}), borderRadius: '0 8px 8px 0', borderLeft: 'none' }}
                                        type="text"
                                        required
                                        value={formData.precio_unitario}
                                        onChange={handlePrecioChange}
                                        placeholder="0"
                                    />
                                </div>
                                {erroresForm.precio_unitario && <span style={styles.errorMensaje}>{erroresForm.precio_unitario}</span>}
                            </div>

                            <div style={styles.formGroup}>
                                <label style={styles.label}>Stock inicial *</label>
                                <input
                                    style={{ ...styles.input, ...(erroresForm.stock ? styles.inputError : {}) }}
                                    type="number"
                                    required
                                    min="0"
                                    value={formData.stock}
                                    onChange={(e) => setFormData({ ...formData, stock: e.target.value })}
                                    placeholder="0"
                                />
                                {erroresForm.stock && <span style={styles.errorMensaje}>{erroresForm.stock}</span>}
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
                            <th style={styles.th}>Repuesto</th>
                            <th style={styles.th}>Precio Unitario</th>
                            <th style={styles.th}>Stock</th>
                            <th style={styles.th}>Usado en</th>
                            <th style={styles.th}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {repuestos.length === 0 ? (
                            <tr>
                                <td colSpan="6" style={{ ...styles.td, textAlign: 'center', padding: '40px', color: '#94a3b8' }}>
                                    <i className="fas fa-microchip" style={{ fontSize: '24px', display: 'block', marginBottom: '10px', opacity: '0.3' }}></i>
                                    No hay repuestos registrados
                                </td>
                            </tr>
                        ) : (
                            repuestos.map((repuesto) => (
                                <tr key={repuesto.id_repuesto}>
                                    <td style={styles.td}>
                                        <span style={styles.repuestoIcon}>
                                            <i className="fas fa-microchip"></i>
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <strong>{repuesto.nombre}</strong>
                                    </td>
                                    <td style={styles.td}>
                                        <strong style={{ color: '#059669' }}>
                                            ${repuesto.precio_unitario ? repuesto.precio_unitario.toLocaleString() : '0'}
                                        </strong>
                                    </td>
                                    <td style={styles.td}>
                                        <span style={styles.stockBadge(repuesto.stock)}>
                                            {repuesto.stock || 0} unidades
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
                                            {repuesto.veces_usado || 0} veces
                                        </span>
                                    </td>
                                    <td style={styles.td}>
                                        <button style={styles.btnInfo} onClick={() => alert('Ver repuesto: ' + repuesto.nombre)} title="Ver">
                                            <i className="fas fa-eye"></i> Ver
                                        </button>
                                        <button style={styles.btnSuccess} onClick={() => editarRepuesto(repuesto)} title="Editar">
                                            <i className="fas fa-pen"></i> Editar
                                        </button>
                                        <button style={styles.btnDanger} onClick={() => eliminarRepuesto(repuesto.id_repuesto, repuesto.nombre)} title="Eliminar">
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

export default Repuestos;