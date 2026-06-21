// src/pages/Ordenes.js
// Página de gestión de órdenes de servicio

import React, { useState, useEffect } from 'react';

function Ordenes() {
    const [ordenes, setOrdenes] = useState([]);
    const [equipos, setEquipos] = useState([]);
    const [tecnicos, setTecnicos] = useState([]);
    const [repuestos, setRepuestos] = useState([]);
    const [repuestosOrden, setRepuestosOrden] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [showForm, setShowForm] = useState(false);
    const [editando, setEditando] = useState(null);
    const [totalOrden, setTotalOrden] = useState(0);
    const [formData, setFormData] = useState({
        id_equipo: '',
        id_tecnico: '',
        sintoma: '',
        mano_obra: '',
        estado: 'en_diagnostico'
    });
    const [erroresForm, setErroresForm] = useState({});
    const [repuestoSeleccionado, setRepuestoSeleccionado] = useState('');
    const [cantidadRepuesto, setCantidadRepuesto] = useState(1);

    const cargarOrdenes = async () => {
        setLoading(true);
        try {
            const response = await fetch('http://localhost/tallert/api/ordenes.php?action=listar');
            const data = await response.json();
            setOrdenes(data || []);
            setError('');
        } catch (error) {
            setError('Error al cargar órdenes');
        } finally {
            setLoading(false);
        }
    };

    const cargarEquipos = async () => {
        try {
            const response = await fetch('http://localhost/tallert/api/equipos.php?action=listar');
            const data = await response.json();
            setEquipos(data || []);
        } catch (error) {
            console.error('Error al cargar equipos:', error);
        }
    };

    const cargarTecnicos = async () => {
        try {
            const response = await fetch('http://localhost/tallert/api/tecnicos.php?action=listar');
            const data = await response.json();
            setTecnicos(data || []);
        } catch (error) {
            console.error('Error al cargar técnicos:', error);
        }
    };

    const cargarRepuestos = async () => {
        try {
            const response = await fetch('http://localhost/tallert/api/repuestos.php?action=listar');
            const data = await response.json();
            setRepuestos(data || []);
        } catch (error) {
            console.error('Error al cargar repuestos:', error);
        }
    };

    const cargarRepuestosOrden = async (idOrden) => {
        try {
            const response = await fetch(`http://localhost/tallert/api/ordenes.php?action=obtener&id=${idOrden}`);
            const data = await response.json();
            setRepuestosOrden(data.repuestos || []);
            setTotalOrden(data.total || 0);
        } catch (error) {
            console.error('Error al cargar repuestos de la orden:', error);
        }
    };

    useEffect(() => {
        cargarOrdenes();
        cargarEquipos();
        cargarTecnicos();
        cargarRepuestos();
    }, []);

    const validarFormulario = () => {
        const errores = {};

        if (!formData.id_equipo) {
            errores.id_equipo = 'Debe seleccionar un equipo';
        }

        if (!formData.id_tecnico) {
            errores.id_tecnico = 'Debe seleccionar un técnico';
        }

        if (!formData.sintoma.trim()) {
            errores.sintoma = 'Debe describir el síntoma';
        }

        if (formData.mano_obra && parseFloat(formData.mano_obra.replace(/\./g, '')) < 0) {
            errores.mano_obra = 'La mano de obra no puede ser negativa';
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
            id_equipo: parseInt(formData.id_equipo),
            id_tecnico: parseInt(formData.id_tecnico),
            sintoma: formData.sintoma,
            mano_obra: parseFloat(formData.mano_obra.replace(/\./g, '').replace(',', '.')) || 0,
            estado: formData.estado
        };

        try {
            const url = editando 
                ? `http://localhost/tallert/api/ordenes.php?action=actualizar&id=${editando}`
                : 'http://localhost/tallert/api/ordenes.php?action=guardar';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataEnviar)
            });
            
            const data = await response.json();
            if (data.success) {
                setShowForm(false);
                setEditando(null);
                setFormData({ id_equipo: '', id_tecnico: '', sintoma: '', mano_obra: '', estado: 'en_diagnostico' });
                setErroresForm({});
                setRepuestosOrden([]);
                setTotalOrden(0);
                await cargarOrdenes();
            } else {
                setError(data.error || 'Error al guardar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const editarOrden = async (orden) => {
        setEditando(orden.id_orden);
        setFormData({
            id_equipo: orden.id_equipo || '',
            id_tecnico: orden.id_tecnico || '',
            sintoma: orden.sintoma || '',
            mano_obra: orden.mano_obra ? orden.mano_obra.toLocaleString() : '',
            estado: orden.estado || 'en_diagnostico'
        });
        setErroresForm({});
        setShowForm(true);
        await cargarRepuestosOrden(orden.id_orden);
    };

    const eliminarOrden = async (id) => {
        if (!window.confirm('¿Eliminar esta orden?')) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/ordenes.php?action=eliminar&id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await cargarOrdenes();
            } else {
                setError(data.error || 'Error al eliminar');
            }
        } catch (error) {
            setError('Error de conexión');
        }
    };

    const agregarRepuesto = async () => {
        if (!repuestoSeleccionado) {
            alert('Seleccione un repuesto');
            return;
        }
        if (cantidadRepuesto < 1) {
            alert('La cantidad debe ser mayor a 0');
            return;
        }

        try {
            const response = await fetch(`http://localhost/tallert/api/ordenes.php?action=agregar_repuesto&id=${editando}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_repuesto: parseInt(repuestoSeleccionado),
                    cantidad: parseInt(cantidadRepuesto)
                })
            });
            const data = await response.json();
            if (data.success) {
                await cargarRepuestosOrden(editando);
                setRepuestoSeleccionado('');
                setCantidadRepuesto(1);
                // Actualizar lista de repuestos disponibles
                await cargarRepuestos();
            } else {
                alert(data.error || 'Error al agregar repuesto');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    };

    const eliminarRepuestoOrden = async (idRepuesto) => {
        if (!window.confirm('¿Eliminar este repuesto de la orden?')) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/ordenes.php?action=eliminar_repuesto&id=${editando}&id_repuesto=${idRepuesto}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                await cargarRepuestosOrden(editando);
                await cargarRepuestos();
            } else {
                alert(data.error || 'Error al eliminar repuesto');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    };

    const cambiarEstado = async (id, nuevoEstado) => {
        if (!window.confirm(`¿Cambiar estado a "${nuevoEstado}"?`)) return;
        try {
            const response = await fetch(`http://localhost/tallert/api/ordenes.php?action=cambiar_estado&id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ estado: nuevoEstado })
            });
            const data = await response.json();
            if (data.success) {
                await cargarOrdenes();
            } else {
                alert(data.error || 'Error al cambiar estado');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    };

    const formatearPrecio = (valor) => {
        if (!valor) return '';
        const num = parseFloat(valor.toString().replace(/\./g, '').replace(',', '.'));
        if (isNaN(num)) return '';
        return num.toLocaleString();
    };

    const handleManoObraChange = (e) => {
        const valor = e.target.value.replace(/[^0-9]/g, '');
        if (valor) {
            setFormData({ ...formData, mano_obra: parseInt(valor).toLocaleString() });
        } else {
            setFormData({ ...formData, mano_obra: '' });
        }
    };

    const estadosTexto = {
        'en_diagnostico': 'En diagnóstico',
        'en_espera_repuestos': 'En espera de repuestos',
        'en_reparacion': 'En reparación',
        'pendiente': 'Pendiente',
        'entregado': 'Entregado'
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
        btnWarning: {
            background: '#f59e0b',
            color: 'white',
            border: 'none',
            padding: '6px 14px',
            borderRadius: '6px',
            cursor: 'pointer',
            fontSize: '12px',
            marginRight: '4px',
            transition: 'background 0.2s'
        },
        btnSmall: {
            background: '#8b5cf6',
            color: 'white',
            border: 'none',
            padding: '4px 10px',
            borderRadius: '4px',
            cursor: 'pointer',
            fontSize: '11px'
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
        statusBadge: (estado) => {
            const badges = {
                'en_diagnostico': { background: '#fef3c7', color: '#92400e' },
                'en_espera_repuestos': { background: '#fef3c7', color: '#92400e' },
                'en_reparacion': { background: '#dbeafe', color: '#1e40af' },
                'pendiente': { background: '#fce4ec', color: '#b71c1c' },
                'entregado': { background: '#d1fae5', color: '#065f46' }
            };
            const badge = badges[estado] || badges['en_diagnostico'];
            return { padding: '4px 12px', borderRadius: '20px', fontSize: '12px', fontWeight: '600', display: 'inline-block', background: badge.background, color: badge.color };
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
        textarea: {
            width: '100%',
            padding: '10px 14px',
            border: '2px solid #e2e8f0',
            borderRadius: '8px',
            fontSize: '14px',
            outline: 'none',
            resize: 'vertical',
            minHeight: '80px',
            fontFamily: 'inherit'
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
        searchBox: { maxWidth: '300px' },
        repuestoItem: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            padding: '8px 12px',
            borderBottom: '1px solid #f1f5f9'
        },
        totalOrden: {
            fontSize: '20px',
            fontWeight: '700',
            color: '#059669'
        }
    };

    if (loading && ordenes.length === 0) {
        return (
            <div style={styles.loading}>
                <i className="fas fa-spinner fa-spin" style={{ fontSize: '30px', color: '#3b82f6' }}></i>
                <p>Cargando órdenes...</p>
            </div>
        );
    }

    return (
        <div style={styles.container}>
            {/* PAGE HEADER */}
            <div style={styles.pageHeader}>
                <div>
                    <h2 style={styles.pageTitle}>
                        <i className="fas fa-clipboard-list" style={styles.pageTitleIcon}></i>
                        Órdenes de Servicio
                    </h2>
                    <p style={styles.pageSubtitle}>Gestión de órdenes de servicio</p>
                </div>
                <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                    <div style={styles.searchBox}>
                        <input
                            type="text"
                            placeholder="Buscar orden..."
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
                    <button style={styles.btnPrimary} onClick={() => { setShowForm(true); setEditando(null); setFormData({ id_equipo: '', id_tecnico: '', sintoma: '', mano_obra: '', estado: 'en_diagnostico' }); setErroresForm({}); setRepuestosOrden([]); setTotalOrden(0); }}>
                        <i className="fas fa-plus"></i> Nueva Orden
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
                        {editando ? 'Editar Orden' : 'Nueva Orden de Servicio'}
                    </h3>
                    <form onSubmit={handleSubmit}>
                        <div style={styles.formGrid}>
                            {/* Equipo */}
                            <div style={styles.formGroupFull}>
                                <label style={styles.label}>Equipo *</label>
                                <select
                                    style={{ ...styles.select, ...(erroresForm.id_equipo ? styles.inputError : {}) }}
                                    value={formData.id_equipo}
                                    onChange={(e) => setFormData({ ...formData, id_equipo: e.target.value })}
                                >
                                    <option value="">Seleccionar equipo...</option>
                                    {equipos.map((equipo) => (
                                        <option key={equipo.id_equipo} value={equipo.id_equipo}>
                                            {equipo.cliente_nombre} - {equipo.marca} {equipo.modelo}
                                        </option>
                                    ))}
                                </select>
                                {erroresForm.id_equipo && <span style={styles.errorMensaje}>{erroresForm.id_equipo}</span>}
                            </div>

                            {/* Técnico */}
                            <div style={styles.formGroup}>
                                <label style={styles.label}>Técnico asignado *</label>
                                <select
                                    style={{ ...styles.select, ...(erroresForm.id_tecnico ? styles.inputError : {}) }}
                                    value={formData.id_tecnico}
                                    onChange={(e) => setFormData({ ...formData, id_tecnico: e.target.value })}
                                >
                                    <option value="">Seleccionar técnico...</option>
                                    {tecnicos.map((tecnico) => (
                                        <option key={tecnico.id_tecnico} value={tecnico.id_tecnico}>
                                            {tecnico.nombre} - {tecnico.especialidad}
                                        </option>
                                    ))}
                                </select>
                                {erroresForm.id_tecnico && <span style={styles.errorMensaje}>{erroresForm.id_tecnico}</span>}
                            </div>

                            {/* Mano de obra */}
                            <div style={styles.formGroup}>
                                <label style={styles.label}>Mano de obra</label>
                                <div style={{ display: 'flex', alignItems: 'center' }}>
                                    <span style={{ background: '#f8fafc', border: '2px solid #e2e8f0', borderRight: 'none', padding: '10px 14px', borderRadius: '8px 0 0 8px' }}>$</span>
                                    <input
                                        style={{ ...styles.input, ...(erroresForm.mano_obra ? styles.inputError : {}), borderRadius: '0 8px 8px 0', borderLeft: 'none' }}
                                        type="text"
                                        value={formData.mano_obra}
                                        onChange={handleManoObraChange}
                                        placeholder="0"
                                    />
                                </div>
                                {erroresForm.mano_obra && <span style={styles.errorMensaje}>{erroresForm.mano_obra}</span>}
                            </div>

                            {/* Estado */}
                            {editando && (
                                <div style={styles.formGroup}>
                                    <label style={styles.label}>Estado</label>
                                    <select
                                        style={styles.select}
                                        value={formData.estado}
                                        onChange={(e) => setFormData({ ...formData, estado: e.target.value })}
                                    >
                                        <option value="en_diagnostico">En diagnóstico</option>
                                        <option value="en_espera_repuestos">En espera de repuestos</option>
                                        <option value="en_reparacion">En reparación</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="entregado">Entregado</option>
                                    </select>
                                </div>
                            )}

                            {/* Síntoma */}
                            <div style={styles.formGroupFull}>
                                <label style={styles.label}>Síntoma reportado *</label>
                                <textarea
                                    style={{ ...styles.textarea, ...(erroresForm.sintoma ? styles.inputError : {}) }}
                                    required
                                    value={formData.sintoma}
                                    onChange={(e) => setFormData({ ...formData, sintoma: e.target.value })}
                                    placeholder="Describe el problema del equipo..."
                                />
                                {erroresForm.sintoma && <span style={styles.errorMensaje}>{erroresForm.sintoma}</span>}
                            </div>
                        </div>

                        {/* Repuestos (solo en edición) */}
                        {editando && (
                            <div style={{ marginTop: '20px', borderTop: '1px solid #e2e8f0', paddingTop: '20px' }}>
                                <h4 style={{ marginBottom: '15px', fontSize: '16px', fontWeight: '600' }}>
                                    <i className="fas fa-microchip" style={{ color: '#7c3aed', marginRight: '8px' }}></i>
                                    Repuestos de la Orden
                                    <span style={{ marginLeft: '10px', background: '#7c3aed', color: 'white', padding: '2px 10px', borderRadius: '20px', fontSize: '12px' }}>
                                        {repuestosOrden.length}
                                    </span>
                                </h4>

                                {/* Agregar repuesto */}
                                <div style={{ display: 'flex', gap: '10px', marginBottom: '15px', flexWrap: 'wrap' }}>
                                    <select
                                        style={{ ...styles.select, maxWidth: '300px' }}
                                        value={repuestoSeleccionado}
                                        onChange={(e) => setRepuestoSeleccionado(e.target.value)}
                                    >
                                        <option value="">Seleccionar repuesto...</option>
                                        {repuestos.map((rep) => (
                                            <option key={rep.id_repuesto} value={rep.id_repuesto}>
                                                {rep.nombre} (Stock: {rep.stock})
                                            </option>
                                        ))}
                                    </select>
                                    <input
                                        type="number"
                                        style={{ ...styles.input, maxWidth: '100px' }}
                                        value={cantidadRepuesto}
                                        onChange={(e) => setCantidadRepuesto(e.target.value)}
                                        min="1"
                                        placeholder="Cant"
                                    />
                                    <button type="button" style={styles.btnSuccess} onClick={agregarRepuesto}>
                                        <i className="fas fa-plus"></i> Agregar
                                    </button>
                                </div>

                                {/* Lista de repuestos */}
                                {repuestosOrden.length === 0 ? (
                                    <p style={{ color: '#94a3b8', textAlign: 'center', padding: '10px' }}>
                                        <i className="fas fa-box" style={{ marginRight: '8px' }}></i>
                                        No hay repuestos agregados
                                    </p>
                                ) : (
                                    <div style={{ border: '1px solid #e2e8f0', borderRadius: '8px' }}>
                                        {repuestosOrden.map((rep) => (
                                            <div key={rep.id_repuesto} style={styles.repuestoItem}>
                                                <div>
                                                    <strong>{rep.nombre}</strong>
                                                    <span style={{ marginLeft: '10px', color: '#64748b', fontSize: '13px' }}>
                                                        x{rep.cantidad} × ${rep.precio_unitario.toLocaleString()} = ${rep.subtotal.toLocaleString()}
                                                    </span>
                                                </div>
                                                <button
                                                    type="button"
                                                    style={styles.btnDanger}
                                                    onClick={() => eliminarRepuestoOrden(rep.id_repuesto)}
                                                >
                                                    <i className="fas fa-times"></i>
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                )}

                                {/* Total */}
                                <div style={{ marginTop: '15px', textAlign: 'right', borderTop: '1px solid #e2e8f0', paddingTop: '15px' }}>
                                    <span style={{ color: '#64748b' }}>Total de la orden: </span>
                                    <span style={styles.totalOrden}>${totalOrden.toLocaleString()}</span>
                                </div>
                            </div>
                        )}

                        <div style={{ display: 'flex', gap: '10px', marginTop: '20px' }}>
                            <button type="submit" style={styles.btnPrimary}>
                                <i className={`fas ${editando ? 'fa-save' : 'fa-plus'}`}></i>
                                {editando ? 'Actualizar' : 'Guardar'}
                            </button>
                            <button type="button" style={styles.btnSecondary} onClick={() => { setShowForm(false); setEditando(null); setErroresForm({}); setRepuestosOrden([]); setTotalOrden(0); }}>
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
                            <th style={styles.th}>ID</th>
                            <th style={styles.th}>Cliente</th>
                            <th style={styles.th}>Equipo</th>
                            <th style={styles.th}>Técnico</th>
                            <th style={styles.th}>Estado</th>
                            <th style={styles.th}>Total</th>
                            <th style={styles.th}>Fecha</th>
                            <th style={styles.th}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {ordenes.length === 0 ? (
                            <tr>
                                <td colSpan="8" style={{ ...styles.td, textAlign: 'center', padding: '40px', color: '#94a3b8' }}>
                                    <i className="fas fa-clipboard-list" style={{ fontSize: '24px', display: 'block', marginBottom: '10px', opacity: '0.3' }}></i>
                                    No hay órdenes registradas
                                </td>
                            </tr>
                        ) : (
                            ordenes.map((orden) => (
                                <tr key={orden.id_orden}>
                                    <td style={styles.td}><strong>#{orden.id_orden}</strong></td>
                                    <td style={styles.td}>{orden.cliente_nombre || 'N/A'}</td>
                                    <td style={styles.td}>
                                        <small>{orden.marca} {orden.modelo}</small>
                                        <br /><code style={{ fontSize: '11px', background: '#f1f5f9', padding: '1px 6px', borderRadius: '4px' }}>{orden.serial}</code>
                                    </td>
                                    <td style={styles.td}>{orden.tecnico_nombre}</td>
                                    <td style={styles.td}>
                                        <span style={styles.statusBadge(orden.estado)}>
                                            {estadosTexto[orden.estado] || orden.estado}
                                        </span>
                                    </td>
                                    <td style={styles.td}><strong>${Number(orden.total).toLocaleString()}</strong></td>
                                    <td style={styles.td}><small>{new Date(orden.fecha_recepcion).toLocaleDateString()}</small></td>
                                    <td style={styles.td}>
                                        <button style={styles.btnInfo} onClick={() => editarOrden(orden)} title="Editar">
                                            <i className="fas fa-pen"></i> Editar
                                        </button>
                                        {orden.estado !== 'entregado' && (
                                            <button style={styles.btnDanger} onClick={() => eliminarOrden(orden.id_orden)} title="Eliminar">
                                                <i className="fas fa-trash"></i> Eliminar
                                            </button>
                                        )}
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

export default Ordenes;