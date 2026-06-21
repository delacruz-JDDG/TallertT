// src/components/Dashboard.js
// Dashboard completo con colores de la imagen y gráficas sin scroll

import React, { useEffect, useState } from 'react';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement } from 'chart.js';
import { Doughnut, Bar } from 'react-chartjs-2';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement);

function Dashboard() {
    const [usuario, setUsuario] = useState(null);
    const [estadisticas, setEstadisticas] = useState({
        ordenes_activas: 0,
        total_clientes: 0,
        tecnicos_activos: 0,
        stock_repuestos: 0
    });
    const [ordenesRecientes, setOrdenesRecientes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [datosGrafico, setDatosGrafico] = useState({
        labels: ['En diagnóstico', 'En reparación', 'Pendiente', 'Entregado'],
        values: [0, 0, 0, 0]
    });

    useEffect(() => {
        const usuarioData = localStorage.getItem('usuario');
        if (usuarioData) {
            setUsuario(JSON.parse(usuarioData));
        }
        cargarDatos();
    }, []);

    const cargarDatos = async () => {
        try {
            const resOrdenes = await fetch('http://localhost/tallert/api/ordenes.php?action=listar');
            const ordenes = await resOrdenes.json();
            const ordenesActivas = ordenes.filter(o => o.estado !== 'entregado').length;

            const resClientes = await fetch('http://localhost/tallert/api/clientes.php?action=listar');
            const clientes = await resClientes.json();

            const resTecnicos = await fetch('http://localhost/tallert/api/tecnicos.php?action=listar');
            const tecnicos = await resTecnicos.json();
            const tecnicosActivos = tecnicos.filter(t => t.estado === 'activo').length;

            const resRepuestos = await fetch('http://localhost/tallert/api/repuestos.php?action=listar');
            const repuestos = await resRepuestos.json();
            const stockTotal = repuestos.reduce((sum, r) => sum + parseInt(r.stock || 0), 0);

            const estados = { 'en_diagnostico': 0, 'en_reparacion': 0, 'pendiente': 0, 'entregado': 0 };
            ordenes.forEach(o => {
                if (estados[o.estado] !== undefined) {
                    estados[o.estado]++;
                }
            });

            setDatosGrafico({
                labels: ['En diagnóstico', 'En reparación', 'Pendiente', 'Entregado'],
                values: [
                    estados['en_diagnostico'] || 0,
                    estados['en_reparacion'] || 0,
                    estados['pendiente'] || 0,
                    estados['entregado'] || 0
                ]
            });

            setEstadisticas({
                ordenes_activas: ordenesActivas,
                total_clientes: clientes.length,
                tecnicos_activos: tecnicosActivos,
                stock_repuestos: stockTotal
            });

            setOrdenesRecientes(ordenes.slice(0, 5));
            setLoading(false);
        } catch (error) {
            console.error('Error:', error);
            setLoading(false);
        }
    };

    const handleLogout = () => {
        localStorage.removeItem('usuario');
        window.location.href = '/';
    };

    const doughnutData = {
        labels: datosGrafico.labels,
        datasets: [{
            data: datosGrafico.values,
            backgroundColor: ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'],
            borderWidth: 0,
        }],
    };

    const doughnutOptions = {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 10,
                    font: { size: 11 }
                }
            }
        }
    };

    const barData = {
        labels: ['Últimos 7 días', 'Total'],
        datasets: [{
            data: [7, 18],
            backgroundColor: ['#3b82f6', '#10b981'],
            borderRadius: 6,
            barThickness: 25
        }]
    };

    const barOptions = {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 10 } }
            },
            y: {
                ticks: { font: { size: 11 } }
            }
        }
    };

    const estadosTexto = {
        'en_diagnostico': 'En diagnóstico',
        'en_espera_repuestos': 'En espera',
        'en_reparacion': 'En reparación',
        'pendiente': 'Pendiente',
        'entregado': 'Entregado'
    };

    // ===== ESTILOS =====
    const styles = {
        container: {
            padding: '20px 30px',
            background: '#f0f2f5',
            minHeight: '100vh',
            fontFamily: 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif'
        },
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
            topBar: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            padding: '15px 0 25px 0',
            flexWrap: 'wrap'
        },
        title: {
            fontSize: '28px',
            fontWeight: '700',
            color: '#1e293b'
        },
        titleSmall: {
            fontSize: '14px',
            fontWeight: '400',
            color: '#64748b',
            marginLeft: '10px'
        },
        userInfo: {
            display: 'flex',
            alignItems: 'center',
            gap: '15px'
        },
        avatar: {
            width: '40px',
            height: '40px',
            borderRadius: '50%',
            background: '#4f46e5',
            color: 'white',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontWeight: '600',
            fontSize: '18px'
        },
        logoutBtn: {
            background: 'transparent',
            border: 'none',
            color: '#64748b',
            cursor: 'pointer',
            fontSize: '14px',
            padding: '8px 12px',
            borderRadius: '8px'
        },
        statsGrid: {
            display: 'grid',
            gridTemplateColumns: 'repeat(4, 1fr)',
            gap: '20px',
            marginBottom: '30px'
        },
        statCard: (color) => ({
            background: color || 'white',
            borderRadius: '12px',
            padding: '18px 22px',
            border: `1px solid ${color || '#e2e8f0'}`,
            transition: 'transform 0.2s, box-shadow 0.2s',
            cursor: 'pointer'
        }),
        statHeader: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center'
        },
        statIcon: {
            width: '45px',
            height: '45px',
            borderRadius: '12px',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: '20px',
            background: '#ffffff'
        },
        statNumber: {
            fontSize: '28px',
            fontWeight: '700',
            marginTop: '4px'
        },
        statLabel: {
            fontSize: '13px',
            color: '#64748b'
        },
        statChange: {
            fontSize: '12px',
            fontWeight: '600',
            marginTop: '5px'
        },
        sectionHeader: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            margin: '30px 0 15px 0'
        },
        sectionTitle: {
            fontWeight: '600',
            color: '#0f172a',
            fontSize: '16px'
        },
        tableContainer: {
            background: 'white',
            borderRadius: '12px',
            padding: '15px',
            border: '1px solid #e2e8f0',
            overflowX: 'auto',
            maxHeight: '250px',
            overflowY: 'auto'
        },
        table: {
            width: '100%',
            borderCollapse: 'collapse',
            fontSize: '13px'
        },
        th: {
            fontWeight: '600',
            color: '#475569',
            borderBottom: '2px solid #e2e8f0',
            padding: '8px 12px',
            textAlign: 'left',
            position: 'sticky',
            top: 0,
            background: 'white',
            zIndex: 10
        },
        td: {
            padding: '8px 12px',
            borderBottom: '1px solid #f1f5f9',
            verticalAlign: 'middle'
        },
        statusBadge: (estado) => {
            const badges = {
                'en_diagnostico': { background: '#fef3c7', color: '#92400e' },
                'en_espera_repuestos': { background: '#fef3c7', color: '#92400e' },
                'en_reparacion': { background: '#dbeafe', color: '#1e40af' },
                'pendiente': { background: '#fce4ec', color: '#b71c1c' },
                'entregado': { background: '#d1fae5', color: '#065f46' }
            };
            const badge = badges[estado] || badges['en_diagnostico'];
            return {
                padding: '4px 12px',
                borderRadius: '20px',
                fontSize: '12px',
                fontWeight: '600',
                display: 'inline-block',
                background: badge.background,
                color: badge.color
            };
        },
        bottomGrid: {
            display: 'grid',
            gridTemplateColumns: '1fr 1fr',
            gap: '20px',
            marginTop: '20px'
        },
        cardCustom: {
            background: 'white',
            borderRadius: '12px',
            padding: '15px 18px',
            border: '1px solid #e2e8f0',
            height: 'auto',
            minHeight: '200px'
        },
        cardTitle: {
            fontWeight: '600',
            color: '#0f172a',
            marginBottom: '8px',
            fontSize: '14px'
        },
        chartWrapper: {
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            height: 'auto'
        },
        chartContainer: {
            maxHeight: '120px',
            height: '120px',
            width: '100%',
            maxWidth: '200px',
            margin: '0 auto'
        },
        chartStats: {
            display: 'flex',
            justifyContent: 'space-around',
            marginTop: '6px',
            width: '100%',
            flexWrap: 'wrap',
            gap: '4px'
        },
        chartStatItem: {
            textAlign: 'center'
        },
        chartStatNumber: {
            fontSize: '14px',
            fontWeight: '700'
        },
        chartStatLabel: {
            color: '#64748b',
            fontSize: '10px'
        }
    };

    if (loading) {
        return (
            <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh', background: '#f0f2f5' }}>
                <div style={{ textAlign: 'center' }}>
                    <i className="fas fa-spinner fa-spin" style={{ fontSize: '40px', color: '#4f46e5' }}></i>
                    <p style={{ marginTop: '10px', color: '#64748b' }}>Cargando datos...</p>
                </div>
            </div>
        );
    }

   return (
    <div style={styles.container}>
        {/* PAGE HEADER */}
        <div style={styles.pageHeader}>
            <div>
                <h2 style={styles.pageTitle}>
                    <i className="fas fa-th-large" style={styles.pageTitleIcon}></i>
                    Dashboard
                </h2>
                <p style={styles.pageSubtitle}>Bienvenido, {usuario?.nombre || 'admin'}</p>
            </div>
        </div>
        {/* TARJETAS CON COLORES */}
        <div style={styles.statsGrid}>
            {/* Órdenes Activas - AZUL */}
            <div style={styles.statCard('#dbeafe')}>
                <div style={styles.statHeader}>
                    <div>
                        <div style={styles.statLabel}>Órdenes Activas</div>
                        <div style={{ ...styles.statNumber, color: '#2563eb' }}>{estadisticas.ordenes_activas}</div>
                    </div>
                    <div style={{ ...styles.statIcon, color: '#2563eb' }}>
                        <i className="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <div style={{ ...styles.statChange, color: '#2563eb' }}>
                    <i className="fas fa-arrow-up"></i> +12% desde la semana pasada
                </div>
            </div>

            {/* Clientes - VERDE */}
            <div style={styles.statCard('#d1fae5')}>
                <div style={styles.statHeader}>
                    <div>
                        <div style={styles.statLabel}>Clientes Registrados</div>
                        <div style={{ ...styles.statNumber, color: '#059669' }}>{estadisticas.total_clientes}</div>
                    </div>
                    <div style={{ ...styles.statIcon, color: '#059669' }}>
                        <i className="fas fa-users"></i>
                    </div>
                </div>
                <div style={{ ...styles.statChange, color: '#059669' }}>
                    <i className="fas fa-arrow-up"></i> +8% desde la semana pasada
                </div>
            </div>

            {/* Técnicos - MORADO */}
            <div style={styles.statCard('#ede9fe')}>
                <div style={styles.statHeader}>
                    <div>
                        <div style={styles.statLabel}>Técnicos Activos</div>
                        <div style={{ ...styles.statNumber, color: '#7c3aed' }}>{estadisticas.tecnicos_activos}</div>
                    </div>
                    <div style={{ ...styles.statIcon, color: '#7c3aed' }}>
                        <i className="fas fa-user-cog"></i>
                    </div>
                </div>
                <div style={{ ...styles.statChange, color: '#7c3aed' }}>
                    <i className="fas fa-arrow-up"></i> +5% desde la semana pasada
                </div>
            </div>

            {/* Repuestos - NARANJA */}
            <div style={styles.statCard('#fef3c7')}>
                <div style={styles.statHeader}>
                    <div>
                        <div style={styles.statLabel}>Repuestos en Stock</div>
                        <div style={{ ...styles.statNumber, color: '#d97706' }}>{estadisticas.stock_repuestos}</div>
                    </div>
                    <div style={{ ...styles.statIcon, color: '#d97706' }}>
                        <i className="fas fa-microchip"></i>
                    </div>
                </div>
                <div style={{ ...styles.statChange, color: '#d97706' }}>
                    <i className="fas fa-arrow-up"></i> +2% desde la semana pasada
                </div>
            </div>
        </div>

        {/* ÓRDENES RECIENTES */}
        <div style={styles.sectionHeader}>
            <span style={styles.sectionTitle}>
                <i className="fas fa-clock" style={{ marginRight: '8px' }}></i>
                Órdenes Recientes
            </span>
        </div>

        <div style={styles.tableContainer}>
            <table style={styles.table}>
                <thead>
                    <tr>
                        <th style={styles.th}>ID</th>
                        <th style={styles.th}>Cliente</th>
                        <th style={styles.th}>Equipo</th>
                        <th style={styles.th}>Estado</th>
                        <th style={styles.th}>Total</th>
                        <th style={styles.th}>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {ordenesRecientes.length > 0 ? (
                        ordenesRecientes.map((orden) => (
                            <tr key={orden.id_orden}>
                                <td style={styles.td}><strong>#{orden.id_orden}</strong></td>
                                <td style={styles.td}>{orden.cliente_nombre || 'N/A'}</td>
                                <td style={styles.td}>{orden.marca} {orden.modelo}</td>
                                <td style={styles.td}>
                                    <span style={styles.statusBadge(orden.estado)}>
                                        {estadosTexto[orden.estado] || orden.estado}
                                    </span>
                                </td>
                                <td style={styles.td}><strong>${Number(orden.total).toLocaleString()}</strong></td>
                                <td style={styles.td}>
                                    <button style={{ background: 'none', border: 'none', color: '#4f46e5', cursor: 'pointer' }}>
                                        <i className="fas fa-eye"></i>
                                    </button>
                                    <button style={{ background: 'none', border: 'none', color: '#059669', cursor: 'pointer', marginLeft: '8px' }}>
                                        <i className="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan="6" style={{ ...styles.td, textAlign: 'center', color: '#94a3b8' }}>
                                <i className="fas fa-inbox" style={{ marginRight: '8px' }}></i>
                                No hay órdenes registradas
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>

        {/* GRÁFICOS */}
        <div style={styles.bottomGrid}>
            <div style={styles.cardCustom}>
                <div style={styles.cardTitle}>
                    <i className="fas fa-chart-pie" style={{ marginRight: '8px' }}></i>
                    Órdenes por Estado
                </div>
                <div style={styles.chartWrapper}>
                    <div style={styles.chartContainer}>
                        <Doughnut data={doughnutData} options={doughnutOptions} />
                    </div>
                    <div style={styles.chartStats}>
                        {datosGrafico.labels.map((label, index) => {
                            const colores = ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'];
                            return (
                                <div key={index} style={styles.chartStatItem}>
                                    <div style={{ ...styles.chartStatNumber, color: colores[index] }}>
                                        {datosGrafico.values[index]}
                                    </div>
                                    <div style={styles.chartStatLabel}>{label}</div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>

            <div style={styles.cardCustom}>
                <div style={styles.cardTitle}>
                    <i className="fas fa-chart-bar" style={{ marginRight: '8px' }}></i>
                    Resumen de Órdenes
                </div>
                <div style={styles.chartWrapper}>
                    <div style={styles.chartContainer}>
                        <Bar data={barData} options={barOptions} />
                    </div>
                    <div style={styles.chartStats}>
                        <div style={styles.chartStatItem}>
                            <div style={{ ...styles.chartStatNumber, color: '#3b82f6' }}>7</div>
                            <div style={styles.chartStatLabel}>Últimos 7 días</div>
                        </div>
                        <div style={styles.chartStatItem}>
                            <div style={{ ...styles.chartStatNumber, color: '#10b981' }}>18</div>
                            <div style={styles.chartStatLabel}>Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
);
}

export default Dashboard;