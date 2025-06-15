import React, { useEffect, useState } from 'react';
import { Layout, Row, Col } from 'antd';
import Header from './Header';
import Category from './Category';
import { RightBar } from './RightBar';
import Items from './Items';
import { Colors } from '../utils/Colors';
import axios from 'axios';

export default function Cashier() {
    const baseUrl = import.meta.env.VITE_APP_URL;
    const [search, setSearch] = useState('');

    const [items, setItems] = useState([]);
    const [categories, setCategories] = useState([]);

    const getCategories = async () => {
        try {
            const req = await axios.get(`${baseUrl}/auth/cashier/categories`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const res = await req.data;
            const data = res.data;

            setCategories(data);

        } catch (error) {
            console.log(error);

        }
    }

    const getItems = async () => {
        try {
            const req = await axios.post(`${baseUrl}/auth/cashier/items`, {
                search: search
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const res = await req.data;
            const data = res.data;
            setItems(data);

        } catch (error) {
            console.log(error);

        }
    }

    useEffect(() => {
        getCategories();
        getItems();
    }, [])

    return (
        <Layout style={{ minHeight: '100vh' }}>

            <Row>
                <Col xs={24} md={16} xl={18} style={{ display: 'flex', flexDirection: 'column' }}>
                    <div style={{ backgroundColor: 'white', boxShadow: '0 7px 7px #ECEFF1', paddingBottom: 12, paddingLeft: 12, paddingRight: 12 }}>
                        <Header />
                        <Category datas={categories} />
                    </div>
                    <div style={{ paddingLeft: 12, paddingRight: 12, marginTop: 16, flex: 1 }}>
                        <Items datas={items} />
                    </div>
                    <div style={{ marginTop: 12, boxShadow: '0 0 7px #ECEFF1', backgroundColor: Colors.blue100, paddingLeft: 12, paddingRight: 12, paddingTop: 22, flex: 1, display: 'flex', flexDirection: 'row', alignItems: 'start', justifyContent: 'space-between' }}>

                    </div>
                </Col>
                <Col xs={24} md={8} xl={6}>
                    <RightBar />
                </Col>
            </Row>
        </Layout>
    );
}
