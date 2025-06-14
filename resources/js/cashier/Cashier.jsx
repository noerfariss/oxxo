import React, { useEffect } from 'react';
import { Layout, Row, Col} from 'antd';
import Header from './Header';
import Category from './Category';
import { RightBar } from './RightBar';
import Items from './Items';
import { Colors } from '../utils/Colors';

export default function Cashier() {
    // Dummy produk
    const products = [
        { id: 1, name: 'Produk A', price: 10000 },
        { id: 2, name: 'Produk B', price: 15000 },
        { id: 3, name: 'Produk C', price: 20000 },
        // Tambahkan lebih banyak sesuai kebutuhan
    ];

    // Dummy keranjang
    const cartItems = [
        { id: 1, name: 'Produk A', qty: 2, price: 10000 },
        { id: 2, name: 'Produk B', qty: 1, price: 15000 },
    ];

    const total = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);

    useEffect(() => {

    }, [])

    return (
        <Layout style={{ minHeight: '100vh' }}>

            <Row>
                <Col xs={24} md={16} xl={18} style={{ display: 'flex', flexDirection: 'column' }}>
                    <div style={{ backgroundColor: 'white', boxShadow: '0 7px 7px #ECEFF1', paddingBottom: 12, paddingLeft: 12, paddingRight: 12 }}>
                        <Header />
                        <Category />
                    </div>
                    <div style={{ paddingLeft: 12, paddingRight: 12, marginTop: 16, flex: 1 }}>
                        <Items />
                    </div>
                    <div style={{ marginTop: 12, boxShadow: '0 0 7px #ECEFF1', backgroundColor: Colors.blue100, paddingLeft: 12, paddingRight: 12, paddingTop: 22, flex: 1, display: 'flex', flexDirection: 'row', alignItems: 'start', justifyContent: 'space-between' }}>

                    </div>
                </Col>
                <Col xs={24} md={8} xl={6}>
                    <RightBar cartItems={cartItems} total={total} />
                </Col>
            </Row>
        </Layout>
    );
}
