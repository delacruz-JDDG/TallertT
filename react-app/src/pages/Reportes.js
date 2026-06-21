// src/pages/Reportes.js
// Página de reportes e informes

import React, { useState, useEffect } from 'react';

function Reportes() {
    const [tecnicos, setTecnicos] = useState([]);
    const [idTecnico, setIdTecnico] = useState('');
    const [fechaInicio, setFechaInicio] = useState('');
    const [fechaFin, setFechaFin] = useState('');
    const [reporte, setReporte] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        cargarTecnicos();
        // Fechas por defecto: primer día del mes y hoy
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        setFechaInicio(primerDia.toISOString().split('T')[0]);
        setFechaFin(hoy.toISOString().split('T')[0]);
    }, []);

    const cargarTecnicos = async () => {
        try {
            const response = await fetch('http://localhost/tallert/api/tecnicos.php?action=listar');
            const data = await response.json();
            setTecnicos(data || []);
        } catch (error) {
            console.error('Error al cargar técnicos:', error);
        }
    };

    const generarReporte = async () => {
        if (!idTecnico) {
            setError('Debe seleccionar un técnico');
            return;
        }

        setLoading(true);
        setError('');
        setReporte(null);

        try {
            const url = `http://localhost/tallert/api/ordenes.php?action=reporte_tecnico&id_tecnico=${idTecnico}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.error) {
                setError(data.error);
            } else {
                setReporte(data);
            }
        } catch (error) {
            setError('Error al generar el reporte');
        } finally {
            setLoading(false);
        }
    };

    const limpiarReporte = () => {
        setReporte(null);
        setIdTecnico('');
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        setFechaInicio(primerDia.toISOString().split('T')[0]);
        setFechaFin(hoy.toISOString().split('T')[0]);
        setError('');
    };

    const getNombreTecnico = () => {
        const tecnico = tecnicos.find(t => t.id_tecnico === parseInt(idTecnico));
        return tecnico ? tecnico.nombre : 'Técnico';
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
        pageTitleIcon: { color: '#059669', marginRight: '10px' },
        pageSubtitle: { fontSize: '14px', color: '#64748b', display: 'block', marginTop: '4px' },
        card: {
            background: 'white',
            borderRadius: '12px',
            padding: '25px',
            border: '1px solid #e2e8f0',
            marginBottom: '20px'
        },
        cardTitle: {
            fontSize: '16px',
            fontWeight: '600',
            color: '#1e293b',
            marginBottom: '15px'
        },
        formGroup: { marginBottom: '15px' },
        label: { display: 'block', fontWeight: '500', color: '#1e293b', marginBottom: '5px', fontSize: '14px' },
        select: {
            width: '100%',
            padding: '10px 14px',
            border: '2px solid #e2e8f0',
            borderRadius: '8px',
            fontSize: '14px',
            outline: 'none',
            background: 'white',
            maxWidth: '400px'
        },
        input: {
            padding: '10px 14px',
            border: '2px solid #e2e8f0',
            borderRadius: '8px',
            fontSize: '14px',
            outline: 'none',
            maxWidth: '200px'
        },
        btnPrimary: {
            background: '#059669',
            color: 'white',
            border: 'none',
            padding: '10px 24px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '14px',
            fontWeight: '500',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '8px',
            transition: 'background 0.2s'
        },
        btnSecondary: {
            background: '#64748b',
            color: 'white',
            border: 'none',
            padding: '10px 24px',
            borderRadius: '8px',
            cursor: 'pointer',
            fontSize: '14px',
            marginLeft: '10px',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '8px',
            transition: 'background 0.2s'
        },
        filterRow: {
            display: 'flex',
            gap: '20px',
            alignItems: 'flex-end',
            flexWrap: 'wrap'
        },
        filterGroup: { flex: 1, minWidth: '180px' },
        errorGlobal: {
            background: '#fee2e2',
            color: '#991b1b',
            padding: '12px 16px',
            borderRadius: '8px',
            marginBottom: '15px',
            border: '1px solid #fecaca'
        },
        loading: { textAlign: 'center', padding: '40px', color: '#64748b' },
        // ===== REPORTE =====
        resumenGrid: {
            display: 'grid',
            gridTemplateColumns: 'repeat(3, 1fr)',
            gap: '20px',
            marginBottom: '20px'
        },
        resumenItem: {
            background: '#f8fafc',
            padding: '15px 20px',
            borderRadius: '10px',
            textAlign: 'center',
            border: '1px solid #e2e8f0'
        },
        resumenNumero: {
            fontSize: '28px',
            fontWeight: '700',
            color: '#0f172a'
        },
        resumenLabel: {
            fontSize: '13px',
            color: '#64748b'
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
        empty: { textAlign: 'center', padding: '40px', color: '#94a3b8' }
    };

    return (
        <div style={styles.container}>
            {/* PAGE HEADER */}
            <div style={styles.pageHeader}>
                <div>
                    <h2 style={styles.pageTitle}>
                        <i className="fas fa-chart-bar" style={styles.pageTitleIcon}></i>
                        Reportes
                    </h2>
                    <p style={styles.pageSubtitle}>Consulta los ingresos generados por cada técnico</p>
                </div>
            </div>

            {/* FILTROS */}
            <div style={styles.card}>
                <h3 style={styles.cardTitle}>
                    <i className="fas fa-filter" style={{ marginRight: '8px', color: '#64748b' }}></i>
                    Filtros de búsqueda
                </h3>

                {error && <div style={styles.errorGlobal}><i className="fas fa-exclamation-circle" style={{ marginRight: '8px' }}></i>{error}</div>}

                <div style={styles.filterRow}>
                    <div style={styles.filterGroup}>
                        <label style={styles.label}>Técnico *</label>
                        <select
                            style={styles.select}
                            value={idTecnico}
                            onChange={(e) => setIdTecnico(e.target.value)}
                        >
                            <option value="">Seleccionar técnico...</option>
                            {tecnicos.map((tecnico) => (
                                <option key={tecnico.id_tecnico} value={tecnico.id_tecnico}>
                                    {tecnico.nombre} - {tecnico.especialidad}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div style={styles.filterGroup}>
                        <label style={styles.label}>Fecha inicio</label>
                        <input
                            type="date"
                            style={styles.input}
                            value={fechaInicio}
                            onChange={(e) => setFechaInicio(e.target.value)}
                        />
                    </div>

                    <div style={styles.filterGroup}>
                        <label style={styles.label}>Fecha fin</label>
                        <input
                            type="date"
                            style={styles.input}
                            value={fechaFin}
                            onChange={(e) => setFechaFin(e.target.value)}
                        />
                    </div>

                    <div style={styles.filterGroup}>
                        <button style={styles.btnPrimary} onClick={generarReporte} disabled={loading}>
                            {loading ? (
                                <><i className="fas fa-spinner fa-spin"></i> Consultando...</>
                            ) : (
                                <><i className="fas fa-search"></i> Consultar</>
                            )}
                        </button>
                        <button style={styles.btnSecondary} onClick={limpiarReporte}>
                            <i className="fas fa-undo"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            {/* RESULTADOS */}
            {loading && (
                <div style={styles.loading}>
                    <i className="fas fa-spinner fa-spin" style={{ fontSize: '30px', color: '#059669' }}></i>
                    <p>Generando reporte...</p>
                </div>
            )}

            {reporte && (
                <div>
                    {/* RESUMEN */}
                    <div style={styles.resumenGrid}>
                        <div style={styles.resumenItem}>
                            <div style={styles.resumenNumero}>{reporte.total_ordenes || 0}</div>
                            <div style={styles.resumenLabel}>
                                <i className="fas fa-clipboard-list" style={{ marginRight: '5px' }}></i>
                                Total de órdenes
                            </div>
                        </div>
                        <div style={styles.resumenItem}>
                            <div style={styles.resumenNumero}>
                                {reporte.ordenes ? reporte.ordenes.reduce((sum, o) => sum + (o.total_repuestos || 0), 0) : 0}
                            </div>
                            <div style={styles.resumenLabel}>
                                <i className="fas fa-microchip" style={{ marginRight: '5px' }}></i>
                                Total de repuestos usados
                            </div>
                        </div>
                        <div style={styles.resumenItem}>
                            <div style={{ ...styles.resumenNumero, color: '#059669' }}>
                                ${(reporte.total_ingresos || 0).toLocaleString()}
                            </div>
                            <div style={styles.resumenLabel}>
                                <i className="fas fa-dollar-sign" style={{ marginRight: '5px' }}></i>
                                Total de ingresos
                            </div>
                        </div>
                    </div>

                    {/* DETALLE */}
                    <div style={styles.tableContainer}>
                        <table style={styles.table}>
                            <thead>
                                <tr>
                                    <th style={styles.th}># Orden</th>
                                    <th style={styles.th}>Cliente</th>
                                    <th style={styles.th}>Equipo</th>
                                    <th style={styles.th}>Repuestos</th>
                                    <th style={styles.th}>Total</th>
                                    <th style={styles.th}>Fecha entrega</th>
                                </tr>
                            </thead>
                            <tbody>
                                {reporte.ordenes && reporte.ordenes.length > 0 ? (
                                    reporte.ordenes.map((orden) => (
                                        <tr key={orden.id_orden}>
                                            <td style={styles.td}><strong>#{orden.id_orden}</strong></td>
                                            <td style={styles.td}>{orden.cliente_nombre}</td>
                                            <td style={styles.td}>{orden.marca} {orden.modelo}</td>
                                            <td style={styles.td}>{orden.total_repuestos || 0}</td>
                                            <td style={styles.td}><strong>${Number(orden.total).toLocaleString()}</strong></td>
                                            <td style={styles.td}>{new Date(orden.fecha_entrega).toLocaleDateString()}</td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" style={styles.empty}>
                                            <i className="fas fa-inbox" style={{ fontSize: '24px', display: 'block', marginBottom: '10px', opacity: '0.3' }}></i>
                                            No hay órdenes entregadas para este técnico en el período seleccionado
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}

            {!reporte && !loading && !error && (
                <div style={{ ...styles.card, textAlign: 'center', padding: '40px', color: '#94a3b8' }}>
                    <i className="fas fa-chart-bar" style={{ fontSize: '48px', display: 'block', marginBottom: '15px', opacity: '0.3' }}></i>
                    <p>Selecciona un técnico y un período de tiempo para generar el reporte</p>
                </div>
            )}
        </div>
    );
}

export default Reportes;