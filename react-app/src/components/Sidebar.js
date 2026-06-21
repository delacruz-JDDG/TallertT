// src/components/Sidebar.js
// Menú lateral de TallerTech

import React from 'react';
import { Link, useLocation } from 'react-router-dom';

function Sidebar() {
    const location = useLocation();

    const isActive = (path) => {
        return location.pathname === path ? 'active' : '';
    };

    const styles = {
        sidebar: {
            width: '250px',
            background: 'linear-gradient(180deg, #1a1a2e 0%, #16213e 100%)',
            color: 'white',
            padding: '20px 0',
            position: 'fixed',
            height: '100vh',
            overflowY: 'auto',
            zIndex: 1000,
            left: 0,
            top: 0
        },
        brand: {
            textAlign: 'center',
            padding: '20px 0 30px 0',
            borderBottom: '1px solid rgba(255,255,255,0.1)'
        },
        brandIcon: {
            fontSize: '40px',
            color: '#818cf8'
        },
        brandTitle: {
            marginTop: '10px',
            fontWeight: '700',
            fontSize: '22px',
            color: 'white'
        },
        brandSub: {
            fontSize: '12px',
            opacity: 0.7,
            display: 'block'
        },
        menu: {
            padding: '20px 15px'
        },
        menuLabel: {
            fontSize: '11px',
            textTransform: 'uppercase',
            opacity: 0.5,
            letterSpacing: '1px',
            padding: '10px 15px',
            color: 'white'
        },
        link: {
            display: 'flex',
            alignItems: 'center',
            padding: '12px 15px',
            color: 'rgba(255,255,255,0.7)',
            textDecoration: 'none',
            borderRadius: '10px',
            marginBottom: '3px',
            transition: 'all 0.3s'
        },
        linkHover: {
            background: 'rgba(255,255,255,0.1)',
            color: 'white'
        },
        linkActive: {
            background: 'rgba(255,255,255,0.1)',
            color: 'white'
        },
        linkIcon: {
            width: '25px',
            marginRight: '12px',
            fontSize: '18px'
        },
        linkBadge: {
            marginLeft: 'auto',
            background: '#4f46e5',
            color: 'white',
            padding: '2px 10px',
            borderRadius: '20px',
            fontSize: '12px'
        },
        logout: {
            position: 'absolute',
            bottom: '20px',
            width: 'calc(100% - 30px)',
            margin: '0 15px',
            padding: '12px 15px',
            borderTop: '1px solid rgba(255,255,255,0.1)',
            color: 'rgba(255,255,255,0.7)',
            textDecoration: 'none',
            display: 'flex',
            alignItems: 'center'
        },
        logoutHover: {
            color: 'white'
        },
        logoutIcon: {
            marginRight: '12px'
        }
    };

    return (
        <div style={styles.sidebar}>
            {/* BRAND */}
            <div style={styles.brand}>
                <i className="fas fa-tools" style={styles.brandIcon}></i>
                <h3 style={styles.brandTitle}>TallerTech</h3>
                <span style={styles.brandSub}>Electrónica</span>
            </div>

            {/* MENU */}
            <div style={styles.menu}>
                <div style={styles.menuLabel}>Navegación</div>

                <Link to="/dashboard" style={{ ...styles.link, ...(isActive('/dashboard') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-th-large" style={styles.linkIcon}></i>
                    Dashboard
                </Link>

                <Link to="/clientes" style={{ ...styles.link, ...(isActive('/clientes') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-users" style={styles.linkIcon}></i>
                    Clientes
                </Link>

                <Link to="/tecnicos" style={{ ...styles.link, ...(isActive('/tecnicos') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-user-cog" style={styles.linkIcon}></i>
                    Técnicos
                </Link>

                <Link to="/equipos" style={{ ...styles.link, ...(isActive('/equipos') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-desktop" style={styles.linkIcon}></i>
                    Equipos
                </Link>

                <Link to="/repuestos" style={{ ...styles.link, ...(isActive('/repuestos') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-microchip" style={styles.linkIcon}></i>
                    Repuestos
                </Link>

                <Link to="/ordenes" style={{ ...styles.link, ...(isActive('/ordenes') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-clipboard-list" style={styles.linkIcon}></i>
                    Órdenes

                </Link>

                <Link to="/reportes" style={{ ...styles.link, ...(isActive('/reportes') === 'active' ? styles.linkActive : {}) }}>
                    <i className="fas fa-chart-bar" style={styles.linkIcon}></i>
                    Reportes
                </Link>
            </div>

            {/* LOGOUT */}
            <Link to="/login" style={styles.logout} onClick={() => localStorage.removeItem('usuario')}>
                <i className="fas fa-sign-out-alt" style={styles.logoutIcon}></i>
                Cerrar Sesión
            </Link>
        </div>
    );
}

export default Sidebar;