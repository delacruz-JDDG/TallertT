// src/components/Layout.js
import React from 'react';
import Sidebar from './Sidebar';

function Layout({ children }) {
    const styles = {
        mainContent: {
            marginLeft: '250px',
            padding: '20px 30px',
            minHeight: '100vh',
            background: '#f0f2f5'
        }
    };

    return (
        <div>
            <Sidebar />
            <div style={styles.mainContent}>
                {children}
            </div>
        </div>
    );
}

export default Layout;